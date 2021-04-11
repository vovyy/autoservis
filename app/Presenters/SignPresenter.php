<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Contributte\FormsBootstrap\BootstrapForm;


class SignPresenter extends Nette\Application\UI\Presenter
{
	protected function createComponentSignInForm(): Form
	{
        $form = new BootstrapForm;
        $form->setHtmlAttribute('class','container-main');

		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit')
            ->setHtmlAttribute('class', 'button btn-danger col-lg-12 col-md-12 col-sm-12');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}
    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }
    public function actionOut(): void
    {
	$this->getUser()->logout();
	$this->flashMessage('Odhlášení bylo úspěšné.');
	$this->redirect('Homepage:');
    }
}