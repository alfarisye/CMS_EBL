<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        $allowed = [
            'login', 'logout', '/', 'public/index.php/login', 'public/index.php/logout', 'public/index.php', 'public/', 'me', 'public/me', 'monitor', 'api/monitor',
            'api/mobile/test', 'api/mobile/signIn', 'api/mobile/testDetail', 'api/mobile/getDataEss', 'api/mobile/getDataTotalEss', 'api/mobile/getUser', 'api/mobile/ApproveCuti',
            'api/mobile/RejectCuti', 'api/mobile/getDataEssHistory', 'api/mobile/getDataPO', 'api/mobile/getDetailPO', 'api/mobile/getVendor', 'api/mobile/getPlant',
            'api/mobile/ApprovePO', 'api/mobile/RejectPO', 'api/mobile/getDataPOhistory', 'api/mobile/CancelPO', 'api/mobile/getParentPR', 'api/mobile/getDetailPR',
            'api/mobile/getPlant', 'api/mobile/getChildPR', 'api/mobile/ApprovePR', 'api/mobile/RejectPR', 'api/mobile/CancelPR', 'api/mobile/getParentPRhistory', 'api/mobile/SalesOrderTotal',
            'api/mobile/getChildPRhistory', 'api/mobile/getPRhistory', 'api/mobile/getDataPR', 'api/mobile/getDataTotalPR', 'api/mobile/getDataTotalPO', 'api/mobile/signInMobile', 'api/bg/transferWB', 'api/bg/receiveWB',
            '/api/get/sales-order',  'api/get/sales-order', 'api/get/t_sal_approval_step', 'api/mobile/ApproveSales', 'api/mobile/notification_mobile',
            '/api/get/sales-order',  'api/get/sales-order', 'api/send/mail/eproc', 'api/get/t_sal_approval_step', 'api/mobile/ApproveSales', 'api/mobile/getReleaseCode', 'api/mobile/notification_mobile',
            'api/mobile/check_id_mobile', 'secret/webhook', 'homeAPI', 'bg_adjust_cms'
        ];
        if (!is_cli() && $request->getMethod() == 'get' && !in_array($request->getURI()->getPath(), $allowed)) {
            // dd(preg_match(session()->get('access'), $request->getURI()->getPath()), session()->get('access'));
            if (!preg_match(session()->get('access'), $request->getURI()->getPath())) {
                $root = site_url('/');
                $html_body = "<center><p>You are not authorized</p><a href='$root'>go back</a></center>";
                $response->setStatusCode(401);
                $response->setBody($html_body);
                $response->send();
                die;
            }
        }
    }
}
