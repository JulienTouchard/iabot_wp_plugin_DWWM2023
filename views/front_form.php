<style>
    .iabotFrame {
        position: fixed;
        right: 0;
        bottom: 2rem;
        z-index: 1000;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        background-color: rgba(0, 0, 0, .7);
        color: #fff;
        border-radius: .5rem;
        box-shadow: 0 0 .5rem rgba(0, 0, 0, .7);

    }
    .iabotTitle{
        font-size:.8rem;
        font-weight: 600;
    }
    #iabotSubmit{
        padding: .5rem;
        font-size: .7rem;
        margin: .5rem auto;
    }
</style>
<div class="iabotFrame">
    <div class="iabotTitle">Vous rencontrez un problème ?</div>
    <div>
    <?php
     if(isset($response)  && !empty($response)){
        echo $response;
     }
     ?>
     </div>
    <form action="" method="post" class="iabotForm">
        <div>
            <textarea name="userSearch" id="userSearch" placeholder="Posez ici votre question :"></textarea>
        </div>
        <div>
            <input id="iabotSubmit" type="submit" value="Envoyer">
        </div>
    </form>
</div>