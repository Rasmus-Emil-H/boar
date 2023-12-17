<?php

var_dump(applicationUser()->orders()->where(['OrderID' => '< 2'])->run());