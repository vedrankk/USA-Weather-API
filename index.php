<?php
include 'src/Database/Model.php';

class Test extends Model{
    public function tableName() : string
    {
        return 'test';
    }
    
    public function attributes() : array
    {
        return ['id', 'name'];
    }
}

