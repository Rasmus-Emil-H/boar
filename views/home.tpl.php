<?php

var_dump(applicationUser()->orders()->where(['OrderID' => '> 0'])->run());