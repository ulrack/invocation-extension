<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

use Ulrack\InvocationExtension\Common\UlrackInvocationExtensionPackage;
use GrizzIt\Configuration\Component\Configuration\PackageLocator;

PackageLocator::registerLocation(
    __DIR__,
    UlrackInvocationExtensionPackage::PACKAGE_NAME,
    []
);
