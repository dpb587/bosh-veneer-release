<?php

namespace Bosh\VersioningBundle\Service\Repository;

use Symfony\Component\Process\Process;

class GitRepository implements RepositoryInterface
{
    protected $workspace;
    protected $pathPrefix;
    protected $executable;

    public function __construct($workspace, $pathPrefix = '', $executable = 'git')
    {
        $this->workspace = $workspace;
        $this->pathPrefix = $pathPrefix;
        $this->executable = $executable;
    }

    public function getFileLog($path)
    {
        $pathPrefix = $this->pathPrefix;

        $p = new Process(
            sprintf(
                '%s --git-dir=%s log -- %s',
                $this->executable,
                escapeshellarg($this->workspace),
                implode(
                    ' ',
                    array_map(
                        function ($v) use ($pathPrefix) {
                            return escapeshellarg(($pathPrefix ? ($pathPrefix . '/') : '') . $v);
                        },
                        (array) $path
                    )
                )
            )
        );

        $p->mustRun();

        $split = preg_split('/^commit /m', $p->getOutput());

        array_shift($split);

        $commits = [];

        foreach ($split as $item) {
            $lines = explode("\n", $item);

            $commits[] = [
                'commit' => $lines[0],
                // cheating committer vs author
                'committed_by' => preg_replace('/^Author:\s+(.+)$/', '$1', $lines[1]),
                'committed_at' => new \DateTime(preg_replace('/^Date:\s+(.+)$/', '$1', $lines[2])),
                'message' => trim(
                    preg_replace(
                        '/^    /m',
                        '',
                        implode(
                            "\n",
                            array_slice($lines, 3)
                        )
                    )
                ),
            ];
        }

        return $commits;
    }
}
