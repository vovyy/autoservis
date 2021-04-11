<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\Main_model;
use Nette\Database\Context;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;

final class MajitelePresenter extends Nette\Application\UI\Presenter
{
    private $main_model;

    public function __construct(Main_model $main_model)
    {
        $this->main_model = $main_model;
    }
    public $database;

    public function renderMajitele(): void
    {
        $this->template->autoservis = $this->database->table('majitele');
    }
    public function injectContext(Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentPostForm(): Form
    {
        $form = new BootstrapForm;
        $form->setHtmlAttribute('class', 'container-main');

        $form->addText('jmeno', 'Jméno:')
            ->setHtmlAttribute('rows', '4')
            ->setHtmlAttribute('cols', '32')
            ->setRequired();

        $form->addText('prijmeni', 'Příjmení:')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $form->addText('adresa', 'Adresa:')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $form->addText('telefon', 'Telefon:')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $form->addEmail('email', 'E-mail:')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $form->addSubmit('send', 'Proveď')
            ->setHtmlAttribute('class', 'button btn-block col-lg-12 col-md-12 col-sm-12 btn_1')
            ->setHtmlAttribute('id', 'submit');

        $formId = $this->getParameter('formId');
        if ($formId) {
            $form->addSubmit('cancel', 'Zpět')
                ->setHtmlAttribute('class', 'button btn-danger col-lg-12 col-md-12 col-sm-12')
                ->setHtmlAttribute('a', 'majitele');
        }


        $form->onSuccess[] = [$this, 'PostFormSucceeded'];


        return $form;
    }
    public function PostFormSucceeded(Form $form, $values): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $formId = $this->getParameter('formId');

        if ($formId) {
            $form = $this->database->table('majitele')->get($formId);
            $form->update($values);
            $this->redirect('majitele');
        } else {

            $forms = $this->database->table('majitele')->insert($values);


            $this->redirect('Opravy:opravy');
        }
    }

    public function actionDelete($id): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $this->database->table('majitele')
            ->where("id", $id)
            ->delete('form');
        $this->redirect('majitele');
    }
    public function actionEdit(int $formId): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $form = $this->database->table('majitele')->get(["id" => $formId]);
        if (!$form) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['postForm']->setDefaults($form->toArray());
    }
}
