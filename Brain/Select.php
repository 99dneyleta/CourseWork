<?php
/**
 * Created by PhpStorm.
 * User: deniz
 * Date: 17/04/2017
 * Time: 12:53 PM
 */


/**
 * Class Select
 * Use for "SELECT" statement in SQL
 */
class Select
{
    private $what;
    private $from;
    private $where;
    private $groupBy;
    private $having;
    private $orderBy;
    private $distinct;
    private $limit;

    public function __construct(string $fields = "*")
    {
        $this->what = $fields;
        return $this;
    }

    public function From(string $from)
    {
        $this->from = $from;
        return $this;
    }

    public function Where(string $where)
    {
        $this->where = "(" . $where . ")";
        return $this;
    }

    public function AndWhere(string $where)
    {
        if ( $this->where == "") {
            $this->where = "(" . $where . ")";
        } else {
            $this->where = "(" . $this->where . " AND " . $where . ")";
        }
        return $this;
    }

    public function OrWhere(string $where)
    {
        if ( $this->where == "") {
            $this->where = "(" . $where . ")";
        } else {
            $this->where = "(" . $this->where . " OR " . $where . ")";
        }
        return $this;
    }

    public function GroupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function Having(string $having)
    {
        $this->having = $having;
        return $this;
    }

    public function OrderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function Distinct(bool $distinct)
    {
        $this->distinct = $distinct;
        return $this;
    }

    public function Limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function __toString()
    {
        if ( !isset($this->what) || !isset($this->from)) {
            throw new Error("Empty fields");
        }
        $str = "SELECT ";
        if ($this->distinct) {
            $str .= " DISTINCT ";
        }
        $str .= $this->what . " FROM " . $this->from ;
        if ( isset($this->where)) {
            $str .= " WHERE " . $this->where;
        }
        if ( isset($this->groupBy)) {
            $str .= " GROUP BY " . $this->groupBy;
        }
        if ( isset($this->having)) {
            $str .= " HAVING " . $this->having;
        }
        if ( isset($this->orderBy)) {
            $str .= " ORDER BY " . $this->orderBy;
        }
        if ( isset($this->limit)) {
            $str .= " LIMIT " . $this->limit;
        }
        $str .= ";";

        return $str;
    }
}