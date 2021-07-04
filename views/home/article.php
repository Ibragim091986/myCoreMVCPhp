<?php

/**
 * @var \AmoCRM\Client\AmoCRMApiClient $apiClient
 */

use AmoCRM\EntitiesServices\Interfaces\HasParentEntity;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;

?>

<div id="content">
    <h2>Сделки</h2>

    <pre><?php

            //$tasksCollection  = $apiClient->tasks();
            //$tasksCollection = $tasksCollection->get();

            //var_dump($apiClient->leads()->getOne(3029111));
            //var_dump($tasksCollection);


            $leads = $apiClient->leads()->get(null, [\AmoCRM\Models\LeadModel::CONTACTS])->all();
            $tr = '';
            foreach ($leads as $item){
                //var_dump($item);
                $name =  $item->name;
                $manadger = $apiClient->users()->getOne($item->responsible_user_id)->getName();
                $links = $apiClient->leads()->getLinks( (new \AmoCRM\Models\LeadModel())->setId($item->id));
                //var_dump($links);
                $contact = '';
                $compani = '';

                foreach ($links as $linkModel){
                    /**
                     * @var \AmoCRM\Models\LinkModel $linkModel
                     */
                    if($linkModel->getToEntityType() == 'contacts'){

                        $contactModel = $apiClient->contacts()->getOne($linkModel->getToEntityId());

                        $contactName = $contactModel->getName();

                        $phone = $contactModel->getCustomFieldsValues();
                        if($phone !== null) $phone = $phone->getBy('fieldCode', 'PHONE');
                        $email = $contactModel->getCustomFieldsValues();
                        if($email !== null) $email = $email->getBy('fieldCode', 'EMAIL');

                        if($phone !== null) {
                            $phone = $phone->getValues()->first()->value;
                            $phone = 'т. ' . $phone;
                        }else $phone = '';

                        if($email !== null){
                            $email = $email->getValues()->first()->value;
                            $email = ' email: ' . $email;
                        } else $email = '';

                        $contact .= $contactName . ' (' . $phone . $email . ') <br>';

                    }
                    elseif($linkModel->getToEntityType() == 'companies'){

                        $companiMode = $apiClient->companies()->getOne($linkModel->getToEntityId());
                        $companiName = $companiMode->getName();

                        $customField = $companiMode->getCustomFieldsValues();
                        if($customField !== null)$customField = $customField->getBy('fieldName', 'Пользовательское 2');

                        //$customField = $companiMode->getCustomFieldsValues()->getBy('fieldName', 'Пользовательское 2');
                        //$customField = null;

                        if($customField !== null){
                            $customField = $customField->getValues()->first()->value;
                            $customField = ' (' . $customField . ')';
                        }else{
                            $customField = '';
                        }

                        $compani .= $companiName  . $customField;

                    }
                }


                //var_dump($links);
                $tr .= "<tr><td>$name</td><td>$manadger</td><td>$contact</td><td>$compani</td></tr>";
            }


        ?></pre>

    <table class="api">
        <tr><th>Сделка</th><th>Ответственный менджер</th><th>Имя контакта</th><th>Компания</th></tr> <!--ряд с ячейками заголовков-->
<!--       <tr><td>данные</td><td>данные</td></tr> -->
        <?= $tr ?>
    </table>

</div>
<div id="footer">&copy; Влад Мержевич</div>
</body>
</html>
