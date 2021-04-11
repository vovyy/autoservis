<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\Main_model;
use Nette\Database\Context;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;

final class SoucastkyPresenter extends Nette\Application\UI\Presenter
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
	public function renderSoucastky(): void
    {
        $this->template->soucastky = $this->database->table('soucastky');
    }
	public function injectContext(Context $database)
	{
		$this->database = $database;
	}

	protected function createComponentPostForm(): Form
	{
		$form = new BootstrapForm;
		$form->setHtmlAttribute('class', 'container-main');
		$mysqli = mysqli_connect("localhost","root","","autoservis");
		$formId = $this->getParameter('formId');

	/*	$form->addText('id', 'id:')
			->setHtmlAttribute('rows', '4')
			->setHtmlAttribute('cols', '32')
			->setRequired(); */

		$sql1 = "SELECT * FROM automobily
        JOIN typ_vozu ON typ_vozu.id = automobily.id
        JOIN majitele ON majitele.id = automobily.id
        JOIN soucastky ON soucastky.id = automobily.id
        JOIN opravy ON soucastky.id = opravy.id
        JOIN zamestnanci ON opravy.id = zamestnanci.id";

		if ($result1 = mysqli_query($mysqli, $sql1)){
			$list1 = array('Vyberte');
			if($row1 = mysqli_num_rows($result1) > 0) {
				while($row1 = mysqli_fetch_array($result1)){
					$list1[] = $row1['prijmeni'];
				}	
			}
		}
		$form->addSelect('zamestnanci_id', 'ID zaměstnanec', $list1)
			->setHtmlAttribute('class', 'form-select')
			->setRequired();

		if ($result2 = mysqli_query($mysqli, $sql1)){
			$list2 = array('Vyberte');
			if($row2 = mysqli_num_rows($result2) > 0) {
				while($row2 = mysqli_fetch_array($result2)){
					$list2[] = $row2['datum'];
				}
			}
		}
		$form->addSelect('opravy_id', 'Opravy', $list2)
		->setHtmlAttribute('class', 'form-select')
		->setRequired();
        
        $form->addText('soucastka', 'Součástka')
			->setHtmlAttribute('type', 'text')
			->setRequired();

		$form->addText('typ_vozu', 'Název vozidla')
			->setHtmlAttribute('type', 'text')
			->setRequired();

        $form->addText('cena', 'Cena')
			->setHtmlAttribute('type', 'number')
			->setRequired();

        $form->addText('skladem_ks', 'Skladem/ks')
			->setHtmlAttribute('type', 'number')
			->setRequired();
		
		if ($result3 = mysqli_query($mysqli, $sql1)){
			$list3 = array('Vyberte');
			if($row3 = mysqli_num_rows($result3) > 0) {
				while($row3 = mysqli_fetch_array($result3)){
					$list3[] = $row3['registranci_znacka'];
				}
			}
		}	
        $form->addSelect('automobily_id', 'automobily ID', $list3)
			->setHtmlAttribute('class', 'form-select')
			->setRequired();

		if ($result4 = mysqli_query($mysqli, $sql1)){
			$list4 = array('Vyberte');
			if($row4 = mysqli_num_rows($result4) > 0) {
				while($row4 = mysqli_fetch_array($result4)){
					$list4[] = $row4['typ_vozu'];
				}
			}
		}
        $form->addSelect('automobily_typ_vozu_id', 'Automobily typ vozu ID', $list4)
			->setHtmlAttribute('class', 'form-select')
			->setRequired();

		if ($result5 = mysqli_query($mysqli, $sql1)){
			$list5 = array('Vyberte');
			if($row5 = mysqli_num_rows($result5) > 0) {
				while($row5 = mysqli_fetch_array($result5)){
					$list5[] = $row5['prijmeni'];
				}
			}
		}
        $form->addSelect('automobily_majitele_id', 'Automobily majitelé ID', $list5)
			->setHtmlAttribute('class', 'form-select')
			->setRequired();

		$form->addSubmit('send', 'Proveď')
			->setHtmlAttribute('class', 'button btn-block col-lg-12 col-md-12 col-sm-12')
			->setHtmlAttribute('id', 'submit');

        if($formId){
            $form->addSubmit('cancel', 'Zpět')
            ->setHtmlAttribute('class', 'button btn-danger col-lg-12 col-md-12 col-sm-12')
            ->setHtmlAttribute('a', 'soucastky');
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
            $form = $this->database->table('soucastky')->get(["id" => $formId]);
            $form->update($values);
            $this->redirect('soucastky');
        } else {

            $forms = $this->database->table('soucastky')->insert($values);


            $this->redirect('TypVozu:typvozu');
        }
    }

    public function actionDelete($id): void
    {
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
        $this->database->table('soucastky')
		->where("id", $id)
		->delete('form');
        $this->redirect('soucastky');
    }
    public function actionEdit(int $formId): void
    {
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
        $form = $this->database->table('soucastky')->get(["id" => $formId]);
        if (!$form) {
            $this->error('Příspěvek nebyl nalezen');
        }
        $this['postForm']->setDefaults($form->toArray());
    }
}
