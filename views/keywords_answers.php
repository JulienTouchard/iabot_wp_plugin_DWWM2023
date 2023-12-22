<h2>Réponses déjà enregistrées :</h2>
<table>
    <thead>
        <td>Mots clés :</td>
        <td>Réponse appropriée du robot :</td>
    </thead>
    <?php foreach ($results as $value) { ?>
    <tbody>
        <td><?= $value->word ?></td>
        <td><?= $value->answer ?></td>
    </tbody>
    <?php } ?>
</table>