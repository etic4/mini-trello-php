<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="lib/assets/images/logo.png" />
        <title>Manage users</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/b5a4564c07.js" crossorigin="anonymous"></script>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
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
                    <?php foreach($users as $user): ?>
                    <div class="table">
                        <input class="checkbox_info" type="checkbox" name="edit_user_<?= $user->get_id() ?>" id="edit_user_<?= $user->get_id() ?>" hidden>
                        <div class="user_info">
                            <table>
                                <tr>
                                    <td><?= $user->get_fullName() ?></td>
                                    <td><?= $user->get_email() ?></td>
                                    <td><?= ucfirst($user->get_role()) ?></td>
                                    <td><label for="edit_user_<?= $user->get_id() ?>"><i class="fas fa-edit"></i></label></td>
                                    <?php if($admin != $user): ?>
                                    <td>
                                        <form class="link" action="user/delete" method="post">
                                            <input type="text" name="id" value="<?= $user->get_id() ?>" hidden>
                                            <input type="submit" value="&#xf2ed" class="far fa-trash-alt" style="background:none">
                                        </form>
                                    </td>
                                    <?php endif ?>
                                </tr>
                            </table>
                        </div>
                        <?php if ($errors->has_errors("user", "edit", $user->get_id())): ?>
                            <?php include('errors.php'); ?>
                        <?php endif; ?>
                        <div class="user_edit">
                            <table>
                                <tr>
                                    <form action="user/edit/<?= $user->get_id() ?>" method="post">
                                        <td>
                                            <input type="text" name="name" value="<?= $user->get_fullName() ?>" required>
                                        </td>
                                        <td>
                                            <input type="email" name="email" value="<?= $user->get_email() ?>"required>
                                        </td>
                                        <?php if($admin != $user): ?>
                                            <td>
                                                <select name="role">
                                                    <option value="admin" <?= ViewTools::selected($user, "admin") ?>>Admin</option>
                                                    <option value="user" <?= ViewTools::selected($user, "user") ?>>User</option>
                                                </select>
                                            </td>
                                        <?php else: ?>
                                            <td>
                                                <input type="text" name="role" value="<?= ucfirst($user->get_role()) ?>" disabled>
                                            </td>
                                        <?php endif ?>
                                        <td>
                                            <label for="edit_user_<?= $user->get_id() ?>"><i class="fas fa-arrow-left"></i></label>
                                        </td>
                                        <td>
                                            <input type="text" name="id" value="<?= $user->get_id() ?>" hidden>
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
                                    <option value="" disabled selected>Select a role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </li>
                            <li class="form-row">
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
