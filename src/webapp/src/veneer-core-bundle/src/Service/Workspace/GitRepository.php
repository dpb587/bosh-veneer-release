<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\SecurityContextInterface;
use TQ\Vcs\Cli\CallException;
use Veneer\CoreBundle\Service\Workspace\Changeset;
use TQ\Git\Repository\Repository;
use TQ\Git\Cli\Binary;
use TQ\Vcs\Gaufrette\Adapter;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

class GitRepository extends Repository implements RepositoryInterface
{
    protected $binary;
    protected $pathPrefix;
    protected $security;

    public function __construct($root, $pathPrefix, SecurityContextInterface $securityContext, $git)
    {
        $this->pathPrefix = rtrim($pathPrefix, '/');
        $this->binary = $git;
        $this->securityContext = $securityContext;

        parent::__construct($root, Binary::ensure($git));
    }

    public function createCheckout($ref = 'HEAD', $mode = 0)
    {
        if ($ref == 'HEAD') {
            $ref = 'master';
        }

        $checkout = new Checkout\GitDirCheckout(
            $this->binary,
            $this->getRepositoryPath() . '/.git',
            $ref,
            $mode & ~CheckoutInterface::MODE_WRITABLE
        );

        if ($mode & CheckoutInterface::MODE_WRITABLE) {
            $checkout = $checkout->getPhysicalCheckout();
        }

        $checkout->cd($this->pathPrefix);

        return $checkout;
    }

    public function commitCheckout(Checkout\PhysicalCheckout $checkout, $message, array $options = [])
    {
        $commitEnv = [
            'GIT_AUTHOR_NAME' => $options['author']['name'],
            'GIT_AUTHOR_EMAIL' => $options['author']['email'],
            'GIT_COMMITTER_NAME' => $options['author']['name'],
            'GIT_COMMITTER_EMAIL' => $options['author']['email'],
        ];

        // check if dirty

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'status',
            [
                '--porcelain',
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        if ('' == $p->getOutput()) {
            return null;
        }

        // add all changes

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'add',
            [
                '-A',
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        // commit

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'commit',
            [
                '-a',
                '-m', $message,
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        // push it back to the main workspace

        $pushArgs = [
            'origin',
            $checkout->getHead() . (isset($options['branch']) ? (':' . $options['branch']) : ''),
        ];

        if (!empty($options['force'])) {
            array_unshift($pushArgs, '--force');
        }

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'push',
            $pushArgs
        );

        $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
        $p->mustRun();

        return true;
    }

    public function listDirectory($directory = '.', $ref = 'HEAD')
    {
        return parent::listDirectory($this->getPrefixedPath($directory), $ref);
    }

    public function showFile($file, $ref = 'HEAD')
    {
        return parent::showFile($this->getPrefixedPath($file), $ref);
    }

    public function fileExists($file, $ref = 'HEAD')
    {
        try {
            return (Boolean) parent::showFile($this->getPrefixedPath($file), $ref);
        } catch (CallException $e) {
            return false;
        }
    }

    public function getPrefixedPath($path)
    {
        return ltrim($this->pathPrefix . '/' . $path, '/');
    }

    public function diff($oldRef, $newRef)
    {
        $prefixedPath = $this->getPrefixedPath('');
        $strlen = strlen($prefixedPath);

        $lines = explode(
            "\n",
            trim($this->exec(
                'diff',
                [
                    '--name-status',
                    ($oldRef == 'live') ? 'master' : $oldRef,
                    $newRef,
                    '--',
                    $prefixedPath,
                ]
            ))
        );

        $statusMap = [
            'A' => Changeset::CREATED,
            'M' => Changeset::MODIFIED,
            'D' => Changeset::DELETED,
        ];

        $changes = [];

        if ((1 != count($lines)) || ('' != $lines[0])) {
            foreach ($lines as $line) {
                $sp = preg_split('/\s+/', $line, 2);

                $changes[ltrim(substr($sp[1], $strlen), '/')] = $statusMap[$sp[0]];
            }
        }

        return new Changeset($this, $oldRef, $newRef, $changes);
    }

    protected function exec($command, array $arguments = [], $stdin = null)
    {
        $call = $this->git->createCall(
            $this->getRepositoryPath(),
            $command,
            $arguments
        );

        $p = new Process(
            $call->getCmd(),
            $call->getCwd(),
            $call->getEnv(),
            $stdin
        );

        $p->mustRun();

        return $p->getOutput();
    }

    public function commitWrites($profile, array $writes, $message = null)
    {
        $username = $this->securityContext->getToken()->getUsername();
        $commitEnv = [
            'GIT_AUTHOR_NAME' => $username,
            'GIT_AUTHOR_EMAIL' => $username . '@' . 'bosh-veneer.local',
            'GIT_COMMITTER_NAME' => $username,
            'GIT_COMMITTER_EMAIL' => $username . '@' . 'bosh-veneer.local',
        ];

        $tmp = uniqid('/tmp/gitrepo-' . microtime(true) . '-');

        // create a temporary workspace for committing

        $call = $this->getGit()->createCall(
            $tmp,
            'clone',
            [
                '--no-checkout',
                $this->getRepositoryPath(),
                $tmp,
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
        $p->mustRun();

        // switch to our draft branch

        if ($profile['draft_started']) {
            // existing
            $call = $this->getGit()->createCall(
                $tmp,
                'checkout',
                [
                    $profile['ref_write'],
                ]
            );

            $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
            $p->mustRun();
        } else {
            // new branch
            $call = $this->getGit()->createCall(
                $tmp,
                'checkout',
                [
                    '-b', $profile['ref_write'],
                    $profile['ref_read'],
                ]
            );

            $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
            $p->mustRun();
        }

        // write out changes

        $updates = [];

        foreach ($writes as $path => $data) {
            $fullpath = $this->getPrefixedPath($path);

            if (null !== $data) {
                if (!file_exists(dirname($tmp . '/' . $fullpath))) {
                    mkdir(dirname($tmp . '/' . $fullpath), 0700, true);
                }

                file_put_contents($tmp . '/' . $fullpath, $data);
            }

            $call = $this->getGit()->createCall(
                $tmp,
                'add',
                [$fullpath]
            );

            $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
            $p->mustRun();

            $updates[] = $fullpath;
        }

        // write a commit

        $call = $this->getGit()->createCall(
            $tmp,
            'commit',
            array_merge(
                [
                    '-o',
                    '-m', $message ?: 'bosh-veneer',
                ],
                $updates
            )
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        // push it back to the main workspace

        $call = $this->getGit()->createCall(
            $tmp,
            'push',
            [
                'origin',
                $profile['ref_write'],
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
        $p->mustRun();

        // cleanup

        $p = new Process('rm -fr ' . escapeshellarg($tmp));
        $p->mustRun();
    }

    public function getDraftProfile($draft, $path)
    {
        $branch = 'veneer-draft-' . $draft;

        try {
            $this->showFile($path, $branch);

            $draftStarted = true;
            $refRead = $branch;

            // draft branch exists
            try {
                $this->exec(
                    'diff',
                    [
                        '--exit-code',
                        'master',
                        $branch,
                        '--',
                        $this->getPrefixedPath($path),
                    ]
                );

                $pathTainted = false;
            } catch (\Exception $e) {
                $pathTainted = true;
            }

            $log = $this->exec(
                'log',
                [
                    $branch,
                    '-1',
                    '--pretty=raw',
                ]
            );

            preg_match('/^committer (.+) (\d+ [\-\+]\d+)$/m', $log, $committerMatch);

            $draftLastAuthor = $committerMatch[1];
            $draftLastDate = \DateTime::createFromFormat('U O', $committerMatch[2]);
        } catch (\Exception $e) {
            $refRead = 'master';
            $draftStarted = false;
            $pathTainted = false;
            $draftLastDate = null;
            $draftLastAuthor = null;
        }

        return [
            'ref_read' => $refRead,
            'ref_write' => $branch,
            'draft_started' => $draftStarted,
            'draft_last_author' => $draftLastAuthor,
            'draft_last_date' => $draftLastDate,
            'path_tainted' => $pathTainted,
        ];
    }
}
