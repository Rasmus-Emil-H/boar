<?php

var_dump(applicationUser()->orders()->select()->where(['Total' => 100])->run());