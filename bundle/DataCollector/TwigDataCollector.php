<?php

/*
 * This file is part of the EzPlatformDesignEngine package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformDesignEngineBundle\DataCollector;

use EzSystems\EzPlatformDesignEngine\Templating\TemplatePathRegistryInterface;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector as BaseCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

class TwigDataCollector extends BaseCollector implements LateDataCollectorInterface
{
    /**
     * @var TemplatePathRegistryInterface
     */
    private $templatePathRegistry;

    public function __construct(\Twig_Profiler_Profile $profile, \Twig_Environment $environment, TemplatePathRegistryInterface $templatePathRegistry)
    {
        parent::__construct($profile, $environment);
        $this->templatePathRegistry = $templatePathRegistry;
    }

    private function getTemplatePathRegistry()
    {
        if (!isset($this->templatePathRegistry)) {
            $this->templatePathRegistry = unserialize($this->data['template_path_registry']);
        }

        return $this->templatePathRegistry;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        parent::collect($request, $response, $exception);
    }

    public function lateCollect()
    {
        parent::lateCollect();
        $this->data['template_path_registry'] = serialize($this->templatePathRegistry);
    }

    public function getTime()
    {
        return parent::getTime();
    }

    public function getTemplateCount()
    {
        return parent::getTemplateCount();
    }

    public function getTemplates()
    {
        $pathMap = $this->getTemplatePathRegistry()->getPathMap();
        $templates = [];
        foreach (parent::getTemplates() as $template => $count) {
            if (isset($pathMap[$template])) {
                $templates[sprintf('%s (%s)', $template, $pathMap[$template])] = $count;
            } else {
                $templates[$template] = $count;
            }
        }

        return $templates;
    }

    public function getBlockCount()
    {
        return parent::getBlockCount();
    }

    public function getMacroCount()
    {
        return parent::getMacroCount();
    }

    public function getHtmlCallGraph()
    {
        return parent::getHtmlCallGraph();
    }

    public function getProfile()
    {
        $profile = parent::getProfile();

        return $profile;
    }
}
