<?php

namespace model;

class SqlGeneratorTest extends  \PHPUnit\Framework\TestCase{


    public function testInsert() {
        $expected_sql = "INSERT INTO user(Mail, FullName, Password) VALUES (:Mail, :FullName, :Password)";
        $expected_params = ["Mail" => "email@machin", "FullName" => "FullName", "Password" => "pass" ];

        list($sql, $params) = (new \SqlGenerator("user"))->insert($expected_params)->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }


    public function testUpdate() {
        $expected_sql = "UPDATE user SET Mail=:Mail, FullName=:FullName WHERE ID=:ID";
        $params_cols =  ["Mail" => "email@machin", "FullName" => "Full Name"];
        $params_where =  ["ID" => "22"];
        $expected_params = array_merge($params_cols, $params_where);

        list($sql, $params) = (new \SqlGenerator("user"))->update($params_cols)->where($params_where)->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testDelete() {
        $expected_sql = "DELETE FROM user WHERE Mail=:Mail";
        $params_where =  ["Mail" => "email@machin"];

        list($sql, $params) = (new \SqlGenerator("user"))->delete()->where($params_where)->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($params_where, $params);
    }

    public function testSelectOnly() {
        $expected_sql = "SELECT * FROM user";
        list($sql, $params) = (new \SqlGenerator("user"))->select()->get();
        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals([], $params);
    }

    public function testSelectWithColumns() {
        $expected_sql = "SELECT ID, FullName FROM user";
        list($sql, $params) = (new \SqlGenerator("user"))->select(["ID", "FullName"])->get();
        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals([], $params);
    }

    public function testSelectWithColumnsAndWhere() {
        $expected_sql = "SELECT ID, FullName FROM user WHERE ID=:ID";
        $expected_params = ["ID" => "1"];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["ID", "FullName"])->where(["ID" => "1"])->get();
        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectWithColumnsOrderBy() {
        $expected_sql = "SELECT ID, FullName FROM user ORDER BY FullName ASC";
        $expected_params = [];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["ID", "FullName"])->Order_by(["FullName" => "ASC"])->get();
        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectJoin() {
        $expected_sql = "SELECT user.ID, user.FullName, board.Title FROM user, board WHERE board.Title=:board_Title AND user.ID=board.Owner";
        $expected_params = ["board_Title" => "the title"];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["user.ID", "user.FullName", "board.Title"])
            ->join(["user", "board"])->on(["user.ID" => "board.Owner"])->where(["board.Title" => "the title"])->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectJoinNoWhere() {
        $expected_sql = "SELECT user.ID, user.FullName, board.Title FROM user, board WHERE user.ID=board.Owner";
        $expected_params = [];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["user.ID", "user.FullName", "board.Title"])
            ->join(["user", "board"])->on(["user.ID" => "board.Owner"])->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectJoinOrderby() {
        $expected_sql = "SELECT user.ID, user.FullName, board.Title FROM user, board WHERE board.Title=:board_Title AND user.ID=board.Owner ORDER BY board.Title ASC";
        $expected_params = ["board_Title" => "the title"];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["user.ID", "user.FullName", "board.Title"])
            ->join(["user", "board"])->on(["user.ID" => "board.Owner"])->where(["board.Title" => "the title"])
            ->order_by(["board.Title" => "ASC"])->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectWhereWithOperators() {
        $expected_sql = "SELECT ID, FullName FROM user WHERE ID!=:ID AND FullName>:FullName";
        $expected_params = ["ID" => "1", "FullName" => "trois"];

        list($sql, $params) = (new \SqlGenerator("user"))->select(["ID", "FullName"])
            ->where(["ID" => "1", "FullName" => "trois"], ["!=", ">"])->get();
        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testSelectDistinctJoin() {
        $expected_sql = "SELECT DISTINCT * FROM user";
        $expected_params = [];

        list($sql, $params) = (new \SqlGenerator("user"))->select($columns=null, $distinct=true)->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);
    }

    public function testWhere() {
        $expected_sql = "WHERE a>:a AND b<:b";
        $expected_params = ["a" => "1", "b" => "1"];

        list($sql, $params) = (new \SqlGenerator())->where(["a" => "1", "b" => "1"], [">", "<"])->get();

        $this->assertEquals($expected_sql, $sql);
        $this->assertEquals($expected_params, $params);

    }

}