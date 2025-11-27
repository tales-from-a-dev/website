<?php

$finder = new TwigCsFixer\File\Finder()
    ->in(__DIR__.'/templates')
;

return new TwigCsFixer\Config\Config()
    ->setFinder($finder)
;
