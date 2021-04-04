<!DOCTYPE html>
<html lang="fr">
<?php include('html_head.php'); ?>

<body class="manage">
        <header id="main_header">
            <?php include('menu.php') ?>
        </header>
        <main>
            <article>
                <section>
                    <h2>Manage Users</h2>
                    <table class="table_header">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></td>
                                <th><i class="fas fa-at"></i></td>
                                <th><i class="fas fa-user-shield"></td>
                            </tr>           
                        </thead>
                        </table>
                    <?php foreach($users as $member): ?>
                    <div class="table">
                        <input class="checkbox_info" type="checkbox" name="edit_user_<?= $member->get_id() ?>" id="edit_user_<?= $member->get_id() ?>" hidden>
                        <div class="user_info">
                            <table>
                                <tr>
                                    <td><?= $member->get_fullName() ?></td>
                                    <td><?= $member->get_email() ?></td>
                                    <td><?= ucfirst($member->get_role()) ?></td>
                                    <td><label for="edit_user_<?= $member->get_id() ?>"><i class="fas fa-edit"></i></label></td>
                                    <?php if($user != $member): ?>
                                    <td>
                                        <form class="link" action="user/delete" method="post">
                                            <input type="text" name="id" value="<?= $member->get_id() ?>" hidden>
                                            <input type="submit" value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                                        </form>
                                    </td>
                                    <?php endif ?>
                                </tr>
                            </table>
                        </div>
                        <?php if ($errors->has_errors("user", "edit", $member->get_id())): ?>
                            <?php include('errors.php'); ?>
                        <?php endif; ?>
                        <div class="user_edit">
                            <table>
                                <tr>
                                    <form action="user/edit/<?= $member->get_id() ?>" method="post">
                                        <td>
                                            <input type="text" name="name" value="<?= $member->get_fullName() ?>" required>
                                        </td>
                                        <td>
                                            <input type="email" name="email" value="<?= $member->get_email() ?>"required>
                                        </td>
                                        <?php if($user != $member): ?>
                                            <td>
                                                <select name="role">
                                                    <option value="admin" <?= ViewTools::selected($member, "admin") ?>>Admin</option>
                                                    <option value="user" <?= ViewTools::selected($member, "user") ?>>User</option>
                                                </select>
                                            </td>
                                        <?php else: ?>
                                            <td>
                                                <input type="text" name="role" value="<?= ucfirst($member->get_role()) ?>" disabled>
                                            </td>
                                        <?php endif ?>
                                        <td>
                                            <label for="edit_user_<?= $member->get_id() ?>"><i class="fas fa-arrow-left"></i></label>
                                        </td>
                                        <td>
                                            <input type="text" name="id" value="<?= $member->get_id() ?>" hidden>
                                            <input type="submit" class="fas fa-check" value="&#xf00c">
                                        </td>
                                    </form>
                                </tr>
                                
                            </table>
                        </div>
                    </div>

                <?php endforeach ?>
                </section>
                <section class="login">
                    <h2>Add a user</h2>
                    <form id="add_user" action="user/add" method="post">
                        <ul class="wrapper">
                            <li class="form-row">
                                <label for="email"><i class="fas fa-at"></i></label>
                                <input type="email" name="email" placeholder="Email" required>
                            </li>
                            <li class="form-row">
                                <label for="fullname"><i class="fas fa-user"></i></label>
                                <input type="text" name="fullName" placeholder="Full Name" required>
                            </li>
                            <li class="form-row">
                                <label for="role"><i class="fas fa-user-shield"></i></label>
                                <select name="role" required>
                                    <option selected="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </li>
                            <li class="form-row">
                                <input type="hidden" name="admin_created" value="true">
                                <input type="submit" value="Add">
                            </li>
                        </ul>
                    </form>
                </section>
                <?php if ($errors->has_errors("user", "add")): ?>
                    <?php include('errors.php'); ?>
                <?php endif; ?>
            </article>
        </main>
    </body>
</html>
