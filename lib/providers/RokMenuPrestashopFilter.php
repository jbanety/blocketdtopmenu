<?php
/**
 * @version   $Id: RokMenuIdFilter.php 4585 2012-10-27 01:44:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokMenuPrestashopFilter extends RecursiveFilterIterator {
    protected $type;
	protected $params;

    public function __construct(RecursiveIterator $recursiveIter, $type, $params) {
        $this->type = $type;
		if (is_string($params))
			$params = json_decode($params);
		$this->params = $params;
        parent::__construct($recursiveIter);
    }

    public function accept() {

		if ($this->hasChildren())
			return true;

		if ($this->current()->getType() !== $this->type)
			return false;

		$currentParams = $this->current()->getParams();
		if (is_string($currentParams))
			$currentParams = json_decode($currentParams);
		foreach(get_object_vars($this->params) as $k => $v) {
			if (!property_exists($currentParams, $k))
				return false;
			if ($currentParams->$k !== $v)
				return false;
		}
		return true;

    }

    public function getChildren() {
        return new self($this->getInnerIterator()->getChildren(), $this->type, $this->params);
    }
}