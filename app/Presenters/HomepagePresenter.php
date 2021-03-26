<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\Main_model;
use Nette\Database\Context;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	private $main_model;

	public function __construct(Main_model $main_model)
	{
		$this->main_model = $main_model;
	}
	public $database;

	public function injectContext(Context $database)
	{
		$this->database = $database;
	}

	protected function createComponentPostForm(): Form
	{
		$form = new BootstrapForm;


		$form->addText('registracni_znacka', 'SPZ:')
			->setHtmlAttribute('class', 'form_aut')
			->setHtmlAttribute('rows', '4')
			->setHtmlAttribute('cols', '32')

			->setRequired();


		$form->addText('vyrobce', 'Výrobce:')
			->setHtmlAttribute('class', 'form_aut')
			->setHtmlAttribute('type', 'text')
			->setRequired();

		$form->addText('rok_vyroby', 'Rok výroby:')
			->setHtmlAttribute('class', 'form_aut')
			->setHtmlAttribute('type', 'text')
			->setRequired();
		$form->addText('barva', 'Barva:')
			->setHtmlAttribute('class', 'form_aut')
			->setHtmlAttribute('type', 'text')
			->setRequired();
		$form->addText('obsah_motoru', 'Obsah motoru:')
			->setHtmlAttribute('class', 'form_aut')
			->setHtmlAttribute('type', 'text')
			->setRequired();

		$form->addSubmit('send', 'Přidat')
			->setHtmlAttribute('class', 'button button3 btn-block col-lg-12 col-md-12 col-sm-12')
			->setHtmlAttribute('id', 'submit');



		$form->onSuccess[] = [$this, 'PostFormSucceeded'];


		return $form;
	}
	public function PostFormSucceeded(Form $form, $values): void
	{
		$formId = $this->getParameter('formId');

		if ($formId) {
			$form = $this->database->table('automobily')->get($formId);
			$form->update($values);
			$this->redirect('default');
		} else {

			$forms = $this->database->table('automobily')->insert($values);


			$this->redirect('Majitele:majitele');
		}
	}
	public function renderDefault(): void
	{
		$this->template->form = $this->database->table('automobily');
	}
}
