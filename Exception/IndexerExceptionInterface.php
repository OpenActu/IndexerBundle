<?php
namespace OpenActu\IndexerBundle\Exception;

interface IndexerExceptionInterface
{
    const UNKNOWN_ERROR_ERRNO = 100;
    const UNKNOWN_ERROR_ERRMSG= "unknown error detected";

    const INVALID_TYPE_FOUND_ERRNO = 101;
    const INVALID_TYPE_FOUND_ERRMSG= "invalid type detected. '%type%' given";

    const INVALID_TYPE_DATA_FOUND_ERRNO = 102;
    const INVALID_TYPE_DATA_FOUND_ERRMSG= "invalid type detected. '%data%' given, '%target%' expected";

    const INVALID_ORIGIN_DATA_ERRNO = 103;
    const INVALID_ORIGIN_DATA_ERRMSG= "%type% attempted. data of type %provenance% given";

    const INVALID_TYPE_INDEX_EXPECTED_ERRNO = 104;
    const INVALID_TYPE_INDEX_EXPECTED_ERRMSG = "%type_expected% expected. %type% found";
}
