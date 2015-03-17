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

    public function __construct()
    {
        $this
            ->setSTDINDescriptor(STDIN)
            ->setSTDOUTDescriptor(STDOUT)
            ->setSTDERRDescriptor(STDERR)
            ->setWorkingDirectory(null)
            ->setEnvironment($_ENV)
        ;
    }

    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
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

    public function run()
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

    public function terminate()
    {        
        if ($this->proc) {
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
