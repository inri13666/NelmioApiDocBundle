<?php

namespace Akuma\Component\ApiDoc\Twig\Extension;

use Akuma\Component\ApiDoc\Annotation\ApiDoc;
use Akuma\Component\ApiDoc\Util\ApiDocHelper;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Michelf\MarkdownExtra;
use Symfony\Component\Routing\RouteCollection;

class MarkdownExtension extends \Twig_Extension
{
    protected $markdownParser;

    public function __construct()
    {
        $this->markdownParser = new MarkdownExtra();

    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('extra_markdown', array($this, 'markdown'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nelmio_api_doc';
    }

    public function markdown($text)
    {
        return $this->markdownParser->transform($text);
    }
}
