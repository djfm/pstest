<?php

namespace PrestaShop\FileSystem;

use Exception;

use Symfony\Component\Finder\Finder;

class FileSystemOverHTTP extends FileSystem
{
    private $pathToWebRoot;
    private $urlToWebRoot;

    public function __construct($pathToWebRoot, $urlToWebRoot)
    {
        parent::__construct();

        $this->pathToWebRoot = $pathToWebRoot;
        $this->urlToWebRoot = $urlToWebRoot;
    }

    public function rmr($dir)
    {
        $finder = new Finder();

        $finder
        ->in($dir)
        ->ignoreDotFiles(false)
        ->sortByType()
        ;

        $toRemove = [];

        $entries = array_reverse(iterator_to_array($finder));

        foreach ($entries as $info) {
            $toRemove[] = $info->getRealPath();
        }

        $script = sprintf(
            "<?php\n\$toRemove = %s;\nforeach (\$toRemove as \$entry) {is_file(\$entry) ? unlink(\$entry) : rmdir(\$entry);}\n",
            var_export($toRemove, true)
        );

        $scriptPath = $this->join($this->pathToWebRoot, 'rmr.php');

        file_put_contents($scriptPath, $script);

        $scriptURL = $this->urlToWebRoot . '/' .'rmr.php';

        file_get_contents($scriptURL);

        unlink($scriptPath);
        rmdir($dir);
    }
}
