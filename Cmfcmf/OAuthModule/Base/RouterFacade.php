<?php
/**
 * OAuth.
 *
 * @copyright Christian Flach (Cmfcmf)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Christian Flach <cmfcmf.flach@gmail.com>.
 * @link https://www.github.com/cmfcmf/OAuth
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.6.1 (http://modulestudio.de).
 */

namespace Cmfcmf\OAuthModule\Base;

use ModUtil;
use System;
use Zikula\Routing\UrlRoute;
use Zikula\Routing\UrlRouter;

/**
 * Url router facade base class
 */
class RouterFacade
{
    /**
     * @var UrlRouter The router which is used internally
     */
    protected $router;

    /**
     * @var array Common requirement definitions
     */
    protected $requirements;

    /**
     * Constructor.
     */
    function __construct()
    {
        $displayDefaultEnding = System::getVar('shorturlsext', 'html');
        
        $this->requirements = array(
            'func'          => '\w+',
            'ot'            => '\w+',
            'slug'          => '[^/.]+', // slugs ([^/.]+ = all chars except / and .)
            'displayending' => '(?:' . $displayDefaultEnding . '|xml|pdf|json|kml)',
            'viewending'    => '(?:\.csv|\.rss|\.atom|\.xml|\.pdf|\.json|\.kml)?',
            'id'            => '\d+'
        );

        // initialise and reference router instance
        $this->router = new UrlRouter();

        // add generic routes
        return $this->initUrlRoutes();
    }

    /**
     * Initialise the url routes for this application.
     *
     * @return UrlRouter The router instance treating all initialised routes
     */
    protected function initUrlRoutes()
    {
        $fieldRequirements = $this->requirements;
        $isDefaultModule = (System::getVar('shorturlsdefaultmodule', '') == 'CmfcmfOAuthModule');
    
        $defaults = array();
        $modulePrefix = '';
        if (!$isDefaultModule) {
            $defaults['module'] = 'CmfcmfOAuthModule';
            $modulePrefix = ':module/';
        }
    
        $defaults['func'] = 'view';
        $viewFolder = 'view';
        // normal views (e.g. orders/ or customers.xml)
        $this->router->set('va', new UrlRoute($modulePrefix . $viewFolder . '/:ot:viewending', $defaults, $fieldRequirements));
    
        // TODO filter views (e.g. /orders/customer/mr-smith.csv)
        // $this->initRouteForEachSlugType('vn', $modulePrefix . $viewFolder . '/:ot/:filterot/', ':viewending', $defaults, $fieldRequirements);
    
    
        return $this->router;
    }
    
    /**
     * Helper function to route permalinks for different slug types.
     *
     * @param string $prefix
     * @param string $patternStart
     * @param string $patternEnd
     * @param string $defaults
     * @param string $fieldRequirements
     */
    protected function initRouteForEachSlugType($prefix, $patternStart, $patternEnd, $defaults, $fieldRequirements)
    {
        // entities with unique slug (slug only)
        $this->router->set($prefix . 'a', new UrlRoute($patternStart . ':slug.' . $patternEnd,        $defaults, $fieldRequirements));
        // entities with non-unique slug (slug and id)
        $this->router->set($prefix . 'b', new UrlRoute($patternStart . ':slug.:id.' . $patternEnd,    $defaults, $fieldRequirements));
        // entities without slug (id)
        $this->router->set($prefix . 'c', new UrlRoute($patternStart . 'id.:id.' . $patternEnd,        $defaults, $fieldRequirements));
    }

    /**
     * Get name of grouping folder for given object type and function.
     *
     * @param string $objectType Name of treated entity type.
     * @param string $func       Name of function.
     *
     * @return string Name of the group folder
     */
    public function getGroupingFolderFromObjectType($objectType, $func)
    {
        // object type will be used as a fallback
        $groupFolder = $objectType;
    
        if ($func == 'view') {
            switch ($objectType) {
                case 'mappedId':
                            $groupFolder = 'mappedids';
                            break;
                default: return '';
            }
        } else if ($func == 'display') {
            switch ($objectType) {
                case 'mappedId':
                            $groupFolder = 'mappedid';
                            break;
                default: return '';
            }
        }
    
        return $groupFolder;
    }

    /**
     * Get name of object type based on given grouping folder.
     *
     * @param string $groupFolder Name of group folder.
     * @param string $func        Name of function.
     *
     * @return string Name of the object type.
     */
    public function getObjectTypeFromGroupingFolder($groupFolder, $func)
    {
        // group folder will be used as a fallback
        $objectType = $groupFolder;
    
        if ($func == 'view') {
            switch ($groupFolder) {
                case 'mappedids':
                            $objectType = 'mappedId';
                            break;
                default: return '';
            }
        } else if ($func == 'display') {
            switch ($groupFolder) {
                case 'mappedid':
                            $objectType = 'mappedId';
                            break;
                default: return '';
            }
        }
    
        return $objectType;
    }

    /**
     * Get permalink value based on slug properties.
     *
     * @param string  $objectType Name of treated entity type.
     * @param string  $func       Name of function.
     * @param array   $args       Additional parameters.
     * @param integer $itemid     Identifier of treated item.
     *
     * @return string The resulting url ending.
     */
    public function getFormattedSlug($objectType, $func, $args, $itemid)
    {
        $slug = '';
    
        switch ($objectType) {
            case 'mappedId':
                $slug = $itemid;
                        break;
        }
    
        return $slug;
    }

    /**
     * Get router.
     *
     * @return \Zikula\Routing\UrlRouter
     */
    public function getRouter()
    {
        return $this->router;
    }
    
    /**
     * Set router.
     *
     * @param \Zikula\Routing\UrlRouter $router.
     *
     * @return void
     */
    public function setRouter(\Zikula\Routing\UrlRouter $router = null)
    {
        $this->router = $router;
    }
    
}
