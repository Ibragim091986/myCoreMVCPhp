<?php

/**
 * @var \AmoCRM\Client\AmoCRMApiClient $apiClient
 */

?>

<div id="content">
    <h2>Сделки</h2>

    <pre><?php

        try {

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
                        //$contactModel = $apiClient->contacts()->getOne($linkModel->toEntityId)->toArray();
                        $contactModel = $apiClient->contacts()->getOne($linkModel->getToEntityId());

                        $name = $contactModel->getName();
                        $phone = $contactModel->getCustomFieldsValues()->getBy('fieldCode', 'PHONE')->getValues()->first()->value;
                        $email = $contactModel->getCustomFieldsValues()->getBy('fieldCode', 'EMAIL')->getValues()->first()->value;

                        $contact .= $name . ' (т. ' . $phone . ' email. ' . $email . ') <br>';

                    }
                    elseif($linkModel->getToEntityType() == 'companies'){

                        $companiMode = $apiClient->companies()->getOne($linkModel->getToEntityId());
                        $name = $companiMode->getName();
                        $customField = $companiMode->getCustomFieldsValues()->getBy('fieldName', 'Пользовательское 2')->getValues()->first()->value;

                        $compani .= $name . ' (' . $customField . ')';

                    }
                }


                //var_dump($links);
                $tr .= "<tr><td>$name</td><td>$manadger</td><td>$contact</td><td>$compani</td></tr>";
            }

        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
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
