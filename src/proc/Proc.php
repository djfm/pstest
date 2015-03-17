<?php

namespace PrestaShop\Proc;

class Proc
{
    private $stdin_descriptor;
    private $stdout_descriptor;
    private $stderr_descriptor;

    private $cwd;

    private $env;

    private $command;

    private $proc = null;
    private $exit_code = null;

    public function __construct($command = '')
    {
        $this
            ->setSTDINDescriptor(STDIN)
            ->setSTDOUTDescriptor(STDOUT)
            ->setSTDERRDescriptor(STDERR)
            ->setWorkingDirectory(null)
            ->setEnvironment($_ENV)
            ->setCommand($command)
        ;
    }

    public function platformIsWindows()
    {
        return preg_match('/^WIN/', PHP_OS);
    }

    private function kill($pid)
    {
        if ($this->platformIsWindows()) {
            exec('tskill ' . $pid);
        } else {
            exec('kill ' . $pid);
        }
    }

    public function getPID()
    {
        if ($this->isRunning()) {
            return $this->getStatus()['pid'];
        } else {
            return -1;
        }
    }

    public function getChildren()
    {
        if (!$this->isRunning()) {
            return [];
        }

        $children = [];

        if ($this->platformIsWindows()) {
            $command = "wmic process where (ParentProcessId=".$this->getPID().") get ProcessId 2>NUL";
			$output = [];
			exec($command, $output);
            for ($i = 1; $i < count($output); $i++) {
                if (preg_match('/^\d+$/', $output[$i])) {
                    $children[] = intval($output[$i]);
                }
            }
        } else {
            $command = "pgrep -P " . $this->getPID();
			$output = [];
			$ret = 1;
			exec($command, $output, $ret);
			if ($ret === 0) {
                $children = array_map('intval', $output);
			}
        }

        return $children;
    }

    public function killChildren()
    {
        foreach ($this->getChildren() as $pid) {
            $this->kill($pid);
        }

        return $this;
    }

    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function setSTDINDescriptor($descriptor)
    {
        $this->stdin_descriptor = $descriptor;
        return $this;
    }

    public function setSTDOUTDescriptor($descriptor)
    {
        $this->stdout_descriptor = $descriptor;
        return $this;
    }

    public function setSTDERRDescriptor($descriptor)
    {
        $this->stderr_descriptor = $descriptor;
        return $this;
    }

    public function setWorkingDirectory($directory)
    {
        $this->cwd = $directory;
        return $this;
    }

    public function setEnvironment(array $env)
    {
        $this->env = $env;
        return $this;
    }

    public function addEnvironmentVariable($key, $value)
    {
        $this->env[$key] = $value;
        return $this;
    }

    public function start()
    {
        $pipes = [];

        $this->proc = proc_open($this->command, [
            0 => $this->stdin_descriptor,
            1 => $this->stdout_descriptor,
            2 => $this->stderr_descriptor
        ], $pipes, $this->cwd, $this->env);

        return $this->proc ? true : false;
    }

    public function close()
    {
        if ($this->proc) {

            $exit_code = proc_close($this->proc);
            $ok = ($exit_code >= 0);

            if ($ok) {
                $this->proc = null;
                $this->exit_code = $exit_code;
            }

            return $ok;
        } else {
            return false;
        }
    }

    public function terminate($killChildren = false)
    {
        if ($this->proc) {

            if ($killChildren) {
                $this->killChildren();
            }

            return proc_terminate($this->proc);
        } else {
            return false;
        }
    }

    public function getStatus()
    {
        $status = proc_get_status($this->proc);

        if ($status === false) {
            return false;
        }

        // Only first call after process end yields correct exit code
        if ($status['running'] === false) {
            if ($this->exit_code === null) {
                $this->exit_code = $status['exitcode'];
            } else {
                $status['exitcode'] = $this->exit_code;
            }
        }

        return $status;
    }

    public function getExitCode()
    {
        if ($this->proc) {
            return $this->getStatus()['exitcode'];
        } else {
            return $this->exit_code;
        }
    }

    public function isRunning()
    {
        if ($this->proc) {
            return $this->getStatus()['running'];
        } else {
            return false;
        }
    }
}
