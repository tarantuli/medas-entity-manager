<?php

namespace PHPSTORM_META {

    use Medas\EntityManager\EntityManager;
    use Medas\EntityManager\Repository;

    override(EntityManager::get(), map([
        '' => '@',
    ]));

    override(EntityManager::create(), map([
        '' => '@',
    ]));

    override(Repository::getOrCreate(), map([
        '' => '@',
    ]));
}
