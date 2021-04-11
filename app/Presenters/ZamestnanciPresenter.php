<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\Main_model;
use Nette\Database\Context;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;

final class ZamestnanciPresenter extends Nette\Application\UI\Presenter
{
	private $main_model;
    //private Nette\Database\Explorer $database;

	public function __construct(Main_model $main_model)
	{
		$this->main_model = $main_model;
	}
	public $database;

/*	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}
    public function renderDefault(): void
    { 
	    //$this->template->autoservis = $this->database->table('automobily')
		//->order('created_at DESC')
		//->limit(5);
		$this->template->autoservis = $this->database->query
		("
		SELECT * FROM `automobily` 
		JOIN typ_vozu ON typ_vozu.id = automobily.id
		JOIN majitele ON majitele.id = automobily.id
		JOIN soucastky ON soucastky.id = automobily.id
		JOIN opravy ON soucastky.id = opravy.id
		JOIN zamestnanci ON opravy.id = zamestnanci.id
		");
    } */
	public function renderZamestnanci(): void
    {
        $this->template->zamestnanci = $this->database->table('zamestnanci');
    }
	public function injectContext(Context $database)
	{
		$this->database = $database;
	}

	protected function createComponentPostForm(): Form
	{
		$form = new BootstrapForm;
		$form->setHtmlAttribute('class', 'container-main');

	/*	$form->addText('id', 'id:')
			->setHtmlAttribute('rows', '4')
			->setHtmlAttribute('cols', '32')
			->setRequired(); */


		$form->addText('jmeno', 'Jméno')
			->setHtmlAttribute('type', 'text')
			->setRequired();

		
		$form->addText('prijmeni', 'Příjmení')
			->setHtmlAttribute('type', 'text')
			->setRequired();
        
        $form->addText('telefon', 'Telefon')
			->setHtmlAttribute('type', 'text')
			->setRequired();

		$form->addSubmit('send', 'Proveď')
			->setHtmlAttribute('class', 'button btn-block col-lg-12 col-md-12 col-sm-12')
			->setHtmlAttribute('id', 'submit');
        $formId = $this->getParameter('formId');
        if($formId){
            $form->addSubmit('cancel', 'Zpět')
            ->setHtmlAttribute('class', 'button btn-danger col-lg-12 col-md-12 col-sm-12')
            ->setHtmlAttribute('a', 'zamestnanci');
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
            $form = $this->database->table('zamestnanci')->get(["id" => $formId]);
            $form->update($values);
            $this->redirect('zamestnanci');
        } else {

            $forms = $this->database->table('zamestnanci')->insert($values);


            $this->redirect('zamestnanci');
        }
    }

    public function actionDelete($id): void
    {
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
        $this->database->table('zamestnanci')
		->where("id", $id)
		->delete('form');
        $this->redirect('zamestnanci');
    }
    public function actionEdit(int $formId): void
    {
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
        $form = $this->database->table('zamestnanci')->get(["id" => $formId]);
        if (!$form) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['postForm']->setDefaults($form->toArray());
    }
}
