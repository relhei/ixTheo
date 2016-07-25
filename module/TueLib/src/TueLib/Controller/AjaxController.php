<?php
/**
 * Proxy Controller Module
 *
 * PHP version 5
 *
 * Copyright (C) Universitätsbiblothek Tübingen 2015.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category TueLib
 * @author   Johannes Ruscheinski <johannes.ruscheinski@uni-tuebingen.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
namespace TueLib\Controller;

/**
 * This controller handles ajax functionality.
 *
 * @package  Controller
 * @author   Oliver Obenland <oliver.obenland@uni-tuebingen.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
class AjaxController extends \VuFind\Controller\AjaxController
{
    protected $moods = ["1" => "Gut", "2" => "Okay", "3" => "Schlecht", "" => "Keine Angaben"];

    public function feedbackAction()
    {
        $this->outputMode = 'plaintext';

        $mood = htmlentities($this->getRequest()->getPost()->get('mood'));
        $message = htmlentities($this->getRequest()->getPost()->get('message'));
        $email = htmlentities($this->getRequest()->getPost()->get('email'));

        if (($mood !== '' || $mood !== '1' && $mood !== '2' && $mood !== '3') || strlen($message) === 0) {
            return $this->output(null, self::STATUS_ERROR);
        }

        $mailContent = "Es war: " . $this->moods[$mood] . ".\n\n";
        $mailContent .= $message . "\n\n";
        if (!empty($email)){
            $mailContent .= "\n";
            $mailContent .= "E-Mail: " . $email;
            $mailContent .= "\n";
        }
        $mailContent .= "----------------------------------------------------------------------------------------------\n";
        $mailContent .= "Aktuelle Seite: " . $this->getRequest()->getHeaders("Referer")->getUri() . "\n";
        $mailContent .= "Browser:        " . htmlentities($this->getRequest()->getHeaders("User-Agent")->getFieldValue()) . "\n";
        $mailContent .= "Cookies:        " . htmlentities($this->getRequest()->getCookie()->getFieldValue()) . "\n";
        $mailContent .= "----------------------------------------------------------------------------------------------\n";

        mail("ixtheo@ub.uni-tuebingen.de", "Feedback", $mailContent);

        return $this->output(null, self::STATUS_OK);
    }
}