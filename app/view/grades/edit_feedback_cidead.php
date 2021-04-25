<?php include_once VIEW_HEADER; ?>
<div class="columns">
    <div class="column"></div>
    <div class="column is-three-fifths">
        <div class="panel-heading has-text-info has-text-centered">
            Editar comentario para el CIDEAD
        </div>
        <div class="box">
            <form class="pt-4 pb-6" id="edit-feedback-form" name="edit-feedback-form" action="<?= BASE_URL . $uri; ?>" method="POST">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="field mx-2">
                    <label class="label <?= ($editFeedbackError || $formError) ? 'has-text-danger' : ''; ?>"> Comentario: </label>
                    <div class="control">
                        <textarea id="edit-feedback-cidead" name="edit-feedback-cidead" class="textarea has-fixed-size <?= ($editFeedbackError || $formError) ? 'is-danger' : ''; ?>" rows="8" spellcheck="true" contenteditable="true" autofocus><?= $studentFeedbackCidead['feedback_cidead']; ?></textarea>
                        <p class="help is-danger"><?= $editFeedbackError; ?></p>
                        <p class="help is-danger"><?= $formError; ?></p>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <button type="submit" name="submit" id="submit" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
                                    <div data-icon="q"></div>
                                    <span>Actualizar</span>
                                </button>
                                <a href="javascript:history.go(-1)" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
                                    <div data-icon="r"></div> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="column"></div>
</div>
<?php include_once VIEW_FOOTER; ?>