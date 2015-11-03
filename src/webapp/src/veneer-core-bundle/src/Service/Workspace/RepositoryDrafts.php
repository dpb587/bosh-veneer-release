<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use TQ\Vcs\Repository\Transaction;
use Symfony\Component\Process\Process;

class RepositoryDrafts
{
    protected $request;
    protected $security;
    protected $repository;

    public function __construct(GitRepository $repository, Request $request, SecurityContextInterface $security)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->security = $security;
    }

    public function commit($profile, array $writes, $message = null)
    {
        $username = $this->security->getToken()->getUsername();
        $commitEnv = [
            'GIT_AUTHOR_NAME' => $username,
            'GIT_AUTHOR_EMAIL' => $username . '@' . 'bosh-veneer.local',
            'GIT_COMMITTER_NAME' => $username,
            'GIT_COMMITTER_EMAIL' => $username . '@' . 'bosh-veneer.local',
        ];

        $tmp = uniqid('/tmp/gitrepo-' . microtime(true) . '-');

        // create a temporary workspace for committing

        $call = $this->repository->getGit()->createCall(
            $tmp,
            'clone',
            [
                '--no-checkout',
                $this->repository->getRepositoryPath(),
                $tmp,
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
        $p->mustRun();

        // switch to our draft branch

        if ($profile['draft_started']) {
            // existing
            $call = $this->repository->getGit()->createCall(
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
            $call = $this->repository->getGit()->createCall(
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
            $fullpath = $this->repository->getPrefixedPath($path);

            if (null !== $data) {
                file_put_contents($tmp . '/' . $fullpath, $data);
            }

            $updates[] = $fullpath;
        }

        // write a commit

        $call = $this->repository->getGit()->createCall(
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

        $call = $this->repository->getGit()->createCall(
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
            $this->repository->showFile($path, $branch);

            $draftStarted = true;
            $refRead = $branch;

            // draft branch exists
            try {
                $this->repository->exec(
                    'diff',
                    [
                        '--exit-code',
                        'master',
                        $branch,
                        '--',
                        $this->repository->getPrefixedPath($path),
                    ]
                );

                $pathTainted = false;
            } catch (\Exception $e) {
                $pathTainted = true;
            }

            $log = $this->repository->exec(
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
