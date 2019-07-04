<?php /** @var PlatesTemplateInterface $this */ ?>
<?php /** @var stdClass $model */ ?>
<?php /** @var array[] $users */ ?>
<?php $this->layout('_layout') ?>

<!-- Tato stranka by tu pochopitelne nebyla, zastupuje prihlasovaci stranku. -->

<form method="post">
    <div class="form-group row">
        <div class="col-1">
            <label for="user" class="form-label">Uživatel:</label>
        </div>
        <div class="col-3">
            <select name="user" id="user" class="form-control">
				<?php foreach ($users as $id => $details) { ?>
					<option value="<?= $id ?>"><?= $details['name'] ?></option>
				<?php } ?>
            </select>
        </div>
        <div class="col-2">
            <button type="submit" class="btn btn-primary">Přihlásit</button>
        </div>
    </div>
</form>
