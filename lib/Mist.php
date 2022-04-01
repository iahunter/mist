<?php

/**
 * lib/Mist.php.
 *
 *
 *
 * PHP version 7
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  default
 *
 * @author    Andrew Jones
 * @copyright 2021 @authors
 * @license   http://www.gnu.org/copyleft/lesser.html The GNU LESSER GENERAL PUBLIC LICENSE, Version 3.0
 */

namespace Ohtarr;

use \GuzzleHttp\Client as GuzzleClient;
use \GuzzleHttp\Cookie\CookieJar as GuzzleCookieJar;

class Mist
{
    public $cookiejar;
    public $baseurl;
    public $token;

    public function __construct($baseurl, $org_id, $token = null)
    {
        $this->cookiejar = new GuzzleCookieJar;
        $this->baseurl = $baseurl;
        $this->org_id = $org_id;
        $this->token = $token;
    }

    public static function guzzle(array $guzzleparams)
    {
        $options = [];
        $params = [];
        $verb = 'get';
        $url = '';
        if(isset($guzzleparams['options']))
        {
            $options = $guzzleparams['options'];
        }
        if(isset($guzzleparams['params']))
        {
            $params = $guzzleparams['params'];
        }
        if(isset($guzzleparams['verb']))
        {
            $verb = $guzzleparams['verb'];
        }
        if(isset($guzzleparams['url']))
        {
            $url = $guzzleparams['url'];
        }

        $client = new GuzzleClient($options);
        $apiRequest = $client->request($verb, $url, $params);
        $response = $apiRequest->getBody()->getContents();
        $array = json_decode($response,true);
        if(is_array($array))
        {
            return $array;
        } else {
            return $response;
        }
    }

    public function getToken($username, $password)
    {
        $guzzleparams = [
            'verb'      =>  'post',
            'url'       =>  $this->baseurl . '/self/apitokens',
            'params'    =>  [
                'headers'   =>  [
                    'Content-Type'  => 'application/json',
                ],
                'auth'  =>  [
                    $username,
                    $password
                ],
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        $this->token = $response['key'];
        return $response['key'];
    }

    public function getWhoAmI()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/self',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getOrgSettings()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/setting',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getOrgAdmins()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/admins',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getOrgLicenses()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/licenses',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getOrgSites()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/sites',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    //public function CreateOrgSite($name, $tz, $country = "US", $address, $lat = null, $lon = null)
    public function CreateOrgSite($params = [])
    {
        $parameters = [
            'country_code'   =>  "US",
            'timezone'  =>  "UTC",
        ];
        if(!$params['name'])
        {
            print "NAME is required!\n";
            return null;
        }
        if(!$params['address'])
        {
            if(!$params['latlng']['lat'] || !$params['latlng']['lng'])
            {
                print "ADDRESS or LAT/LON is required!\n";
                return null;
            }
        }
        $body = $params + $parameters;
        
        $guzzleparams = [
            'verb'      =>  'post',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/sites',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getSiteById($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function UpdateSite($siteid, $params = [])
    {
        $body = $params;
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/sites/' . $siteid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getSiteSettingsById($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/setting',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function UpdateSiteSettings($siteid, $params = [])
    {
        $body = $params;
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/setting',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function addSiteVariables($siteid, $variables = [])
    {
        $sitesettings = $this->getSiteSettingsById($siteid);
        $existingvars = $sitesettings['vars'];
        
        $body = [
            'vars'  =>  $variables + $existingvars,
        ];

        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/setting',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function addSiteToGroup($siteid, $sitegroupid)
    {
        $currentgroupids = $this->getSite($siteid)['sitegroup_ids'];
        print_r($currentgroupids);
        foreach($currentgroupids as $groupid)
        {
            if($sitegroupid == $groupid)
            {
                print "Sitegroup already assigned!";
                return null;
            }
        }
        $newgroupids = $currentgroupids;
        $newgroupids[] = $sitegroupid;
        print_r($newgroupids);

        $body = [
            'sitegroup_ids' => $newgroupids,
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/sites/' . $siteid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function DeleteOrgSite($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'delete',
            'url'       =>  $this->baseurl . '/sites/' . $siteid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getOrgInventory($filter = null)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        if($filter)
        {
            $guzzleparams['params']['query'] = $filter;
        }

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function AddOrgInventory($claim_codes = [])
    {
        $body = $claim_codes;
        $guzzleparams = [
            'verb'      =>  'post',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function DeleteOrgInventoryBySerials($serials = [])
    {
        $body = [
            'op'        =>  'delete',
            'serials'   =>  $serials,
            'macs'      =>  [],
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function DeleteOrgInventoryByMacs($macs = [])
    {
        $body = [
            'op'        =>  'delete',
            'serials'   =>  [],
            'macs'      =>  $macs,
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function AssignOrgInventoryToSite($macs = [], $siteid)
    {
        $body = [
            'op'        =>  'assign',
            'site_id'   =>  $siteid,
            'macs'      =>  $macs,
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function UnassignOrgInventoryFromSite($macs = [])
    {
        $body = [
            'op'        =>  'unassign',
            'macs'      =>  $macs,
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/inventory',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getAllRfTemplates()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/rftemplates',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getRfTemplateById($id)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/rftemplates/' . $id,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getDevices($filter = null)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/devices/search',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        if($filter)
        {
            $guzzleparams['params']['query'] = $filter;
        }

        $response = $this->guzzle($guzzleparams);
        return $response['results'];
    }

    //Type = switch, gateway, ?
    public function getSiteStats($siteid, $type=null)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/stats/devices',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
            ]
        ];
        if($type)
        {
            $guzzleparams['params']['query']['type'] = $type;
        }
        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getAdoptionCommand($siteid = null)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/ocdevices/outbound_ssh_cmd',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getSiteDevices($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/devices',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getSiteAssets($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/assets',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getSiteDiscoveredSwitches($siteid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/stats/discovered_switches/search',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getAllTemplates()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/templates',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getTemplateById($id)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/templates/' . $id,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function addSiteToTemplate($templateid,$siteid)
    {
        $template = $this->getTemplateById($templateid);
        if(!$template)
        {
            return null;
        }
        $site = $this->getSiteById($siteid);
        if(!$site)
        {
            return null;
        }
        $siteids = $template['applies']['site_ids'];
        $siteids[] = $siteid;

        $body = [
            'applies'   =>  [
                'site_ids'  =>  $siteids,
            ],
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/templates/' . $templateid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function removeSiteFromTemplate($templateid,$siteid)
    {
        $body = [
            'applies'   =>  [
                'site_ids'  =>  [
                    $siteid,
                ],
            ],
        ];
        $guzzleparams = [
            'verb'      =>  'put',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/templates/' . $templateid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getAllSiteGroups()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/sitegroups',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function createSiteGroup($name)
    {
        $body = [
            'name'   => $name,
        ];
        $guzzleparams = [
            'verb'      =>  'post',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/sitegroups',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($body),
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getAllNetworkTemplates()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/networktemplates',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];

        $response = $this->guzzle($guzzleparams);
        return $response;
    }

    public function getNetworkTemplateById($networktemplateid)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/networktemplates/' . $networktemplateid,
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ]
            ]
        ];
        try{
            $response = $this->guzzle($guzzleparams);
        } catch(\Exception $e) {
            return null;
        }
        return $response;
    }

    public function assignNetworkTemplateToSite($siteid, $networktemplateid)
    {
        $params = [
            "networktemplate_id"    =>  $networktemplateid,
        ];
        $this->UpdateSite($siteid, $params);
    }

    public function getLogs()
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/orgs/' . $this->org_id . '/logs/',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'query' =>  [
                    'limit'     =>  '100',
                    'start'     =>  '1647790642',
                    'end'       =>  '1647890642',
                ],
            ]
        ];
        try{
            $response = $this->guzzle($guzzleparams);
        } catch(\Exception $e) {
            return null;
        }
        return $response;
    }

    public function searchWiredClients($siteid, $device_mac)
    {
        $guzzleparams = [
            'verb'      =>  'get',
            'url'       =>  $this->baseurl . '/sites/' . $siteid . '/wired_clients/search',
            'params'    =>  [
                'headers'   =>  [
                    'Authorization' =>  'Token ' . $this->token,
                    'Content-Type'  => 'application/json',
                ],
                'query' =>  [
                    'mac'     =>  $device_mac,
                ],
            ]
        ];
        try{
            $response = $this->guzzle($guzzleparams);
        } catch(\Exception $e) {
            return null;
        }
        return $response;
    }

}