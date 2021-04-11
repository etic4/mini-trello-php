<?php

interface IPermissions {
    function add(User $user): bool;
    function view(User $user): bool;
    function edit(User $user): bool;
    function delete(User $user): bool;
}