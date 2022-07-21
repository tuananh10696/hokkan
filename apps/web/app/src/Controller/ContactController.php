<?php

namespace App\Controller;


use Cake\Event\Event;
use App\Form\ContactForm;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ContactController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->setHeadTitle('お問い合わせ');
    }

    public function index()
    {

        $contact = new ContactForm();

        $view = 'index';
        $data = null;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $contact->validate($this->request->getData());

            if (empty($contact->getErrors())) {
                $is_confirm_success = isset($data['is_confirm_success']) && intval($data['is_confirm_success']) == 1;
                $view = $is_confirm_success ? 'complete' : 'confirm';

                if ($is_confirm_success) {
                    $contact->execute($data);
                }
            } else {
                $this->set('error', $contact->getErrors());
            }
        }
        $this->set(compact('contact', 'data'));
        $this->render($view);
    }
}
