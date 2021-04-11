<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\Main_model;
use Nette\Database\Context;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;

final class OpravyPresenter extends Nette\Application\UI\Presenter
{
    private $main_model;

    public function __construct(Main_model $main_model)
    {
        $this->main_model = $main_model;
    }
    public $database;

    public function renderOpravy(): void
    {
        $this->template->opravy = $this->database->table('opravy');
    }
    public function injectContext(Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentPostForm(): Form
    {
        $form = new BootstrapForm;
        $form->setHtmlAttribute('class', 'container-main');
        $mysqli = mysqli_connect("localhost", "root", "", "autoservis");
        /*		$form->addText('id', 'ID')
			->setHtmlAttribute('rows', '4')
			->setHtmlAttribute('cols', '32')
			->setRequired(); */

        $form->addDateTime('datum', 'Začátek opravy (Datum)')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $form->addDateTime('doba_opravy', 'Konec opravy (Doba opravy)')
            ->setHtmlAttribute('type', 'text')
            ->setRequired();

        $sql1 = "SELECT zamestnanec_id, prijmeni  FROM opravy JOIN zamestnanci ON opravy.id = zamestnanci.id";
        if ($result1 = mysqli_query($mysqli, $sql1)) {
            $list1 = array('Vyberte');
            if ($row1 = mysqli_num_rows($result1) > 0) {
                while ($row1 = mysqli_fetch_array($result1)) {
                    $list1[] = $row1['prijmeni'];
                }
            }
        }
        $form->addSelect('zamestnanec_id', 'Zaměstnanec', $list1)
            ->setHtmlAttribute('class', 'form-select')
            ->setRequired();

        $sql2 = "SELECT soucastka, soucastky_id FROM soucastky JOIN opravy ON soucastky.id = opravy.id";
        if ($result2 = mysqli_query($mysqli, $sql2)) {
            $list2 = array('Vyberte');
            if ($row2 = mysqli_num_rows($result2) > 0) {
                while ($row2 = mysqli_fetch_array($result2)) {
                    $list2[] = $row2['soucastka'];
                }
            }
        }
        //print_r($list);
        $form->addSelect('soucastky_id', "Součástky ID", $list2)
            //->setHtmlAttribute('type', 'text')
            //->setItems($row)
            ->setHtmlAttribute('class', 'form-select')
            //->setPrompt("Vyberte číslo náhradního dílu");
            ->setRequired();

        $form->addText('pocet_ks', 'Počet/ks')

            ->setHtmlAttribute('type', 'number')
            ->setHtmlAttribute('placeholder', 'Zadejte číslo')
            ->setRequired();

        $form->addSubmit('send', 'Proveď')
            ->setHtmlAttribute('class', 'button btn-block col-lg-12 col-md-12 col-sm-12')
            ->setHtmlAttribute('id', 'submit');

        $formId = $this->getParameter('formId');
        if ($formId) {
            $form->addSubmit('cancel', 'Zpět')
                ->setHtmlAttribute('class', 'button btn-danger col-lg-12 col-md-12 col-sm-12')
                ->setHtmlAttribute('a', 'opravy');
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
            $form = $this->database->table('opravy')->get($formId);
            $form->update($values);
            $this->redirect('opravy');
        } else {

            $forms = $this->database->table('opravy')->insert($values);


            $this->redirect('Soucastky:soucastky');
        }
    }

    public function actionDelete($id): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $this->database->table('opravy')
            ->where("id", $id)
            ->delete('form');
        $this->redirect('opravy');
    }
    public function actionEdit(int $formId): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        $form = $this->database->table('opravy')->get(["id" => $formId]);
        if (!$form) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['postForm']->setDefaults($form->toArray());
    }
}
