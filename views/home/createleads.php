<?php

/**
 * @var \AmoCRM\Client\AmoCRMApiClient $apiClient
 */

use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Collections\TasksCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\ServiceMessageNote;
use AmoCRM\Models\TaskModel;


function printError(AmoCRMApiException $e): void
{
    $errorTitle = $e->getTitle();
    $code = $e->getCode();
    $debugInfo = var_export($e->getLastRequestInfo(), true);

    $error = <<<EOF
Error: $errorTitle
Code: $code
Debug: $debugInfo
EOF;

    echo '<pre>' . $error . '</pre>';
}

?>

    <pre><?php
        $checkPost = false;


        // Создание контакта
        if(isset($_POST['contactName']) && isset($_POST['contactTelephone']) ){
            $checkPost = true;

            $links = new LinksCollection();
            $contact = new ContactModel();
            $contactsCollection = new ContactsCollection();

            $contact->setName($_POST['contactName']);

            $phoneField = new MultitextCustomFieldValuesModel();
            $phoneField->setFieldCode('PHONE');

            $phoneField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORK')
                            ->setValue($_POST['contactTelephone'])
                    )
            );

            $contact->setCustomFieldsValues((new CustomFieldsValuesCollection)->add($phoneField));

            $contactsCollection->add($contact);

            try {
                $apiClient->contacts()->add($contactsCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }


            //var_dump($contact);

        }else $checkPost = false;


        // Создание компании
        if(isset($_POST['companyName']) && $checkPost){

            $company = new CompanyModel();
            $company->setName($_POST['companyName']);

            $companiesCollection = new CompaniesCollection();
            $companiesCollection->add($company);

            try {
                $apiClient->companies()->add($companiesCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

            $links->add($contact);

            try {
                $apiClient->companies()->link($company, $links);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

            //var_dump($company);

        }else $checkPost = false;


        // Создание сделки
        if(isset($_POST['leadName']) && isset($_POST['leadPrice']) && isset($_POST['leadNote']) && $_POST['leadCommon'] && $checkPost){
            $checkPost = true;

            //$leadsCollection = $leadsService->get();


            $lead = new LeadModel();
            $lead->setName($_POST['leadName'])
                ->setPrice($_POST['leadPrice']);

            $leadsCollection = new LeadsCollection();
            $leadsCollection->add($lead);
            //$leadsService = $apiClient->leads();

            try {
                $apiClient->leads()->add($leadsCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

            $links->add($company);

            try {
                $apiClient->leads()->link($lead, $links);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

            //var_dump($lead);
        }else $checkPost = false;

        // Создаем служебные и общие примечания
        if($checkPost == true){
            $notesCollection = new NotesCollection();
            $serviceMessageNote = new ServiceMessageNote();
            $serviceMessageNote->setEntityId($lead->getId())
                ->setText($_POST['leadNote'])
                ->setService('Api Library')
                ->setCreatedBy(0);

            $commonNote = new \AmoCRM\Models\NoteType\CommonNote();
            $commonNote->setEntityId($lead->getId())
                ->setText($_POST['leadCommon']);


            $notesCollection->add($serviceMessageNote);
            $notesCollection->add($commonNote);

            try {
                $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
                $notesCollection = $leadNotesService->add($notesCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }
        }else $checkPost = false;

        // Создание задачи
        if(isset($_POST['taskName']) && isset($_POST['taskType']) && isset($_POST['taskDate']) && $_POST['taskDuration'] && $checkPost){
            $tasksCollection = new TasksCollection();
            $task = new TaskModel();

            $date = strtotime($_POST['taskDate']);
            $task->setTaskTypeId($_POST['taskType'])
                ->setText($_POST['taskName'])
                ->setCompleteTill($date)
                ->setEntityType(EntityTypesInterface::LEADS)
                ->setEntityId($lead->getId())
                ->setDuration($_POST['taskDuration']) //30 минут
                ->setResponsibleUserId($lead->getResponsibleUserId());
            $tasksCollection->add($task);

            $tasksService = $apiClient->tasks();
            try {
                $apiClient->tasks()->add($tasksCollection);
            } catch (AmoCRMApiException $e) {
                printError($e);
                die;
            }

        }



        ?></pre>
<div class="container">
    <?php if(!$checkPost): ?>
    <div class="col-md-8 order-md-1">
        <div class="py-5 text-center">
            <h2>Создание сделки</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 order-md-1">
            <form method="post" class="needs-validation" novalidate="">
                <h4 class="mb-3">Введите данные для сделеки</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="leadName">Название сделки</label>
                        <input type="text" class="form-control" id="leadName" name="leadName" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите название сделки.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="leadPrice">Бюджет сделки</label>
                        <input type="number" class="form-control" id="leadPrice" name="leadPrice" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите сумму бюджета.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="leadNote">Сервисное примечание</label>
                        <input type="text" class="form-control" id="leadNote" name="leadNote" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите сервисное примечание к сделки.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="leadCommon">Общее примечание</label>
                        <input type="text" class="form-control" id="leadCommon" name="leadCommon" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите общее примечание к сделки.
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Создать компанию</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="companyName">Наименование компании</label>
                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите имя компании.
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Создать контакт</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contactName">Имя контакта</label>
                        <input type="text" class="form-control" id="contactName" name="contactName" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите имя контакта.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contactTelephone">Телефон контакта</label>
                        <input type="text" class="form-control" id="contactTelephone" name="contactTelephone" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите номер телефона.
                        </div>
                    </div>
                </div>

                <div class="row"></div>
                <div class="row"></div>
                <div class="row"></div>
                <h4 class="mb-3">Добавить задачу</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="taskName">Название задачи</label>
                        <input type="text" class="form-control" id="taskName" name="taskName" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите название задачи.
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="taskType">Тип задачи</label>
                        <select class="custom-select d-block w-100" id="taskType" name="taskType" required="">
                            <option value="1">Связаться.</option>
                            <option value="2">Встреча</option>
                        </select>
                        <div class="invalid-feedback">
                            Пожалуйста, выберите тип задачи.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="taskDate">Дата выполнения</label>
                        <input type="text" class="form-control datepicker-here" data-timepicker="true" data-position="right top" id="taskDate" name="taskDate" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Введите дату выполнения.
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="taskDuration">Продолжительность</label>
                        <select class="custom-select d-block w-100" id="taskDuration" name="taskDuration" required="">
                            <option value="1800">30 мин.</option>
                            <option value="36000">10 ч.</option>
                            <option value="86400">В течении дня</option>
                        </select>
                        <div class="invalid-feedback">
                            Пожалуйста, выберите продолжительность выполнения задачи.
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit">Создать</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="col-md-8 order-md-1">
        <div class="py-5 text-center">
            <h2>Сделка и задача создана</h2>
        </div>
    </div>
    <div class="col-md-8 order-md-1">
        <a href="/">Вернуться на главную</a>
    </div>
        <div class="col-md-8 order-md-1">
            <a href="/createleads">Создать еще сделку и задачу</a>
        </div>
    <?php endif; ?>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">© 2017-2018 Company Name</p>
    </footer>
</div>


</body>
</html>
