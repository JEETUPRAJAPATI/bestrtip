-- Run this once to add new fields to property_booking table

ALTER TABLE `property_booking`
  ADD COLUMN IF NOT EXISTS `extra_services`       LONGTEXT    NULL DEFAULT NULL COMMENT 'JSON array of {name, price}',
  ADD COLUMN IF NOT EXISTS `extra_services_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `discount_percent`     DECIMAL(5,2)  NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `discount_amount`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `final_total`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `total_pax`            INT           NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `no_of_rooms`          INT           NOT NULL DEFAULT 0;
