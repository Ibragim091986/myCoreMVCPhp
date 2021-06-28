<?php

/**
 * @var \AmoCRM\Client\AmoCRMApiClient $apiClient
 */

?>

<div id="content">
    <h2>Сделки</h2>

    <pre><?php

        try {

            //var_dump($apiClient->leads()->get()->toArray());
            //var_dump($apiClient->leads()->getOne(1856983));
            //var_dump($apiClient->companies()->getOne(3493643)->toArray());
            //var_dump($apiClient->contacts()->getOne(3493625)->toArray());
            //var_dump($apiClient->contacts()->getLinks( (new \AmoCRM\Models\ContactModel())->setId(3493625) ));
            //var_dump($apiClient->leads()->getLinks( (new \AmoCRM\Models\LeadModel())->setId(1856983)));
            $leads = $apiClient->leads()->get()->all();
            $tr = '';
            foreach ($leads as $item){
                $name =  $item->name;
                $manadger = $apiClient->users()->getOne($item->responsible_user_id)->getName();
                $links = $apiClient->leads()->getLinks( (new \AmoCRM\Models\LeadModel())->setId($item->id));

                $contact = '';
                $compani = '';

                foreach ($links as $linkModel){
                    if($linkModel->toEntityType == 'contacts'){
                        $contactModel = $apiClient->contacts()->getOne($linkModel->toEntityId)->toArray();
                        $contact .= $contactModel['name'] . ' (';
                        foreach ($contactModel['custom_fields_values'] as $custom_fields_values){

                            if($custom_fields_values['field_code'] == 'PHONE')$contact .=  ' т. ' . $custom_fields_values['values'][0]['value'];
                            elseif ($custom_fields_values['field_code'] == 'EMAIL') $contact .=  ' email. ' . $custom_fields_values['values'][0]['value'];
                        }
                        $contact .= ' ) <br>';
                    }
                    elseif($linkModel->toEntityType == 'companies'){
                        $companiMode = $apiClient->companies()->getOne($linkModel->toEntityId)->toArray();
                        $compani .= $companiMode['name'] . ' ';
                        foreach ($companiMode['custom_fields_values'] as $custom_fields_values){
                            if($custom_fields_values['field_code'] == NULL){
                                $compani .='(' . $custom_fields_values['field_name'] . ' - ' .  $custom_fields_values['values'][0]['value'];
                            }

                        }
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
