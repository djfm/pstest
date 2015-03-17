<?php

namespace PrestaShop\Selenium\Xvfb;

use Exception;

use PrestaShop\Proc\Proc;
use PrestaShop\Proc\ExecutableHelper;

class XvfbServerFactory
{
    private $start_display = 10;
    private $end_display = 20;

    public function hasXvfbProgram()
    {
        return ExecutableHelper::inPath('Xvfb');
    }

    private function _makeServer($displayNummber)
    {
        $command = 'Xvfb :' . $displayNummber . ' -ac';
        $proc = new Proc($command);
        $proc->disableSTDOUT()->disableSTDERR();

        $proc->start();

        $server = new XvfbServer($proc, $displayNummber);

        sleep(1);

        if ($server->isRunning()) {
            return $server;
        } else {
            return false;
        }
    }

    public function makeServer()
    {
        for ($display = $this->start_display; $display < $this->end_display; ++$display) {
            $server = $this->_makeServer($display);
            if ($server) {
                return $server;
            }
        }

        throw new Exception('Could not start Xvfb server.');
    }
}
