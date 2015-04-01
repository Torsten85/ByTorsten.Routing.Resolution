<?php
namespace ByTorsten\Routing\Resolution;

class ResolutionDecisionMaker extends \ByTorsten\Routing\Dimension\AbstractDimensionDecisionMaker {

    /**
     * @return string
     */
    function resolveDimension() {
        $detect = new \Mobile_Detect();

        if ($detect->isMobile()) {
            if ($detect->isTablet()) {
                return 'md';
            }

            return 'sm';
        }
        return 'lg';
    }
}