ALTER TABLE `coupons` ADD `forall` INT  NULL DEFAULT '1' AFTER `limit`;
ALTER TABLE `coupons` ADD `FromRegisterDate` DATE  NULL AFTER `forall`;
ALTER TABLE `coupons` ADD `FromOrderTimes` INT  NULL AFTER `FromRegisterDate`;
ALTER TABLE `coupons` ADD `Fromprice` FLOAT  NULL AFTER `FromOrderTimes`;
ALTER TABLE `coupons` ADD `ToRegisterDate` DATE  NULL AFTER `Fromprice`;
ALTER TABLE `coupons` ADD `ToOrderTimes` INT  NULL AFTER `ToRegisterDate`;
ALTER TABLE `coupons` ADD `Toprice` FLOAT  NULL AFTER `ToOrderTimes`;
ALTER TABLE `coupons` ADD `city` INT  NULL AFTER `Toprice`;
ALTER TABLE `coupons` ADD `area` INT  NULL AFTER `city`;
ALTER TABLE `coupons` ADD `type` INT  NULL AFTER `area`;