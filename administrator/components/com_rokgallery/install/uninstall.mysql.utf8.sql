DROP TABLE IF EXISTS #__rokgallery_profiles;
DROP TABLE IF EXISTS #__rokgallery_jobs;
DROP TABLE IF EXISTS #__rokgallery_filters;
DROP TABLE IF EXISTS #__rokgallery_slices_index;
DROP TABLE IF EXISTS #__rokgallery_slice_tags;
DROP TABLE IF EXISTS #__rokgallery_slices;
DROP TABLE IF EXISTS #__rokgallery_galleries;
DROP TABLE IF EXISTS #__rokgallery_file_views;
DROP TABLE IF EXISTS #__rokgallery_file_tags;
DROP TABLE IF EXISTS #__rokgallery_file_loves;
DROP TABLE IF EXISTS #__rokgallery_files_index;
DROP TABLE IF EXISTS #__rokgallery_files;
DROP TABLE IF EXISTS #__rokgallery_schema_version;

DELETE IGNORE FROM `#__rokcommon_configs` WHERE extension='rokgallery';
