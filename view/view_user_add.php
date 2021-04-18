<!DOCTYPE html>
<html lang="fr">
<?php $title="Add a user"; include('head.php'); ?>

<body  class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php') ?>
        </header>
        <main>
            <article>
                <section>
                    <h2 class="title">Add a user</h2>
                    <form action="user/add" method="post">
                        <input type="text" name="confirm" hidden>

                        <div class="field">
                            <label class="label">Full Name</label>
                            <div class="control">
                                <input class="input" type="text" name="fullName" value="<?= $fullName ?>">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input class="input" type="email" name="email" value="<?= $email ?>">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Role</label>
                            <div class="select" >
                                <select name="role">
                                    <option value="user" <?= ViewUtils::selected_state($role, "user")?>>User</option>
                                    <option value="admin" <?= ViewUtils::selected_state($role, "admin")?>>Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="is-flex is-flex-direction-row mt-5 mb-5">
                            <a class="button is-light" href="user/manage">Cancel</a>
                            <input class="button is-success ml-3" type="submit" value="Add a user">
                        </div>
                    </form>
                </section>
                <?php if ($errors->has_errors()): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
            </article>
        </main>
    </body>
</html>
