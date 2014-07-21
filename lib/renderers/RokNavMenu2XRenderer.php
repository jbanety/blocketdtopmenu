<?php
/**
 * @version   $Id: RokNavMenu2XRenderer.php 4585 2012-10-27 01:44:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
class RokNavMenu2XRenderer extends RokMenuDefaultRenderer
{
   public function renderHeader(){
        parent::renderHeader();
	   $controller = Context::getContext()->controller;

        foreach($this->layout->getScriptFiles() as $script){
			$controller->addJS($script['relative']);
        }

        foreach($this->layout->getStyleFiles() as $style){
            $controller->addCSS($style['relative']);
        }
        /*$doc->addScriptDeclaration($this->layout->getInlineScript());
        $doc->addStyleDeclaration($this->layout->getInlineStyle());*/
    }
}
