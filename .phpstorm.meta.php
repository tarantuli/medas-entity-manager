<?php

namespace PHPSTORM_META {

    use Medas\EntityManager\EntityManager;

    override(EntityManager::get(), map([
        '' => '@',
    ]));

    override(EntityManager::create(), map([
        '' => '@',
    ]));
}
