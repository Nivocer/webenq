<?php
/**
 * Cerate some basic stats
 *
 * @package Statistics
 * @version $Id: stats.php,v 1.1 2011/08/16 12:16:20 bart Exp $

 * @author Jaap-Andre de Hoop <j.dehoop@nivocer.com>, Rolf Kleef <r.kleef@nivocer.com>
 * @link http://www.webenq.org
 * @copyright (C) 2004-2010 Nivocer B.V.
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License

 This program is free software; you can redistribute it and/or modify it under the
 terms of the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

 **/

//echo "<pre>";
//$data=array(1,1,1,1,1,2,2,2,2,2,2,3,3,3,3,3,4,4,4,4,4,5,5,5,5,6,6,9,9,9,9);
//$missing=array(9);
//$answerPossibilities=array(1,2,3,4,5,6);
//$statistics=new Stats($data,$missing,$answerPossibilities);
//$statistics->univariate();
//print_r($statistics);
//echo "</pre>";

//todo weighting

/**
 * Statistics class
 *
 */
class Stats
{
    var $data;
    var $missing;
    var $answerPossibilities;
    /**
     * Constructor
     *
     * @array $data array with all date
     * @array $missing array with answerpossibilites which aren't valid (don't know, n/a)
     * @array $answerPossibilits array with valid Answerpossibilities (numbers 1,2,3,4,5)
     * @return Stats
     */
    public function __construct($data, $missing=NULL, $answerPossibilities=NULL)
    {
        $this->data=$data;
        $this->missingAnswers=$missing;
        $this->answerPossibilities=$answerPossibilities;
        if (empty($this->answerPossibilities)) {
            $this->answerPossibilities=array(1,2,3,4,5);
        }
    }
    public function univariate ()
    {
        //data to freq table format values-count
        $this->freq=array_count_values($this->data);
        $dataValid=array_diff($this->data, $this->missingAnswers);
        //seperate missing
        //create array with missing values as keys
        $missingKeys=array_fill_keys($this->missingAnswers, 1);
        //create array with valid freqs (nonmissing)
        $this->freqValid=array_diff_key($this->freq, $missingKeys);
        //create array with missing freqs
        $this->freqMissing=array_diff_key($this->freq, $this->freqValid);
        //number of answers

        $this->freqRecoded=$this->computeFreqRecoded('positiveNegative');
        $this->totalN=count($this->data);
        //number of valid answers
        $this->totalValid=array_sum($this->freqValid);
        //number of missgin answers
        $this->totalMissing=array_sum($this->freqMissing);

        //min&max)
        $this->minimum=min($dataValid);
        $this->maximum=max($dataValid);

        //modus: most mentioned answer:
        //@todo bug: what if multiple has the same freq.
        $temp=$this->freqValid;
        arsort($temp);
        $tempModus=array_keys($temp);
        $this->modus=$tempModus[0];

        //median (mid answer)
        //two cases even number of answers, odd number of answers
        $medianN=ceil($this->totalValid/2);

        if ($this->isEven($this->totalValid)) {
            //mean of two
            $this->median=($dataValid[$medianN]+$dataValid[$medianN+1])/2;
        } else {
            $this->median=$dataValid[$medianN];
        }

        //mean
        $sumValid=array_sum($dataValid);
        $this->mean=$sumValid/$this->totalValid;

        //variance/standard deviation
        $this->variance=$this->computeVariance();
        $this->standardDeviation=sqrt($this->variance);

        //bug: should be 0.13096081606991988 (without the 6 in $data)
        //$this->skwewness=$this->computeSkewness();
        //todo should be -1.2221230048866105
        //$this->kurtosis=$this->computeKurtosis();


        ksort($this->freqValid);
    }

    /*
     * compute recoded frequences
    * default postiveNegative
    * if even: recode into two categories (positvie, negative)
    * if odd: recode into three categories (positve, neutral, negative)
    */

    protected function computeFreqRecoded($type='positiveNegative')
    {
        switch ($type){
            case "positiveNegative":
            case "negativePositive":
                $medianN=ceil(count($this->answerPossibilities)/2);
                if ($this->isEven(count($this->answerPossibilities))) {
                    // recode in two categories
                    $return[1]=array_sum(array_slice($this->freqValid, 0, $medianN));
                    $return[3]=array_sum(array_slice($this->freqValid, $medianN));
                } else {
                    //recode into 3 categories 2=neutral
                    $return[1]=array_sum(array_slice($this->freqValid, 0, $medianN-1));
                    $return[2]=array_sum(array_slice($this->freqValid, $medianN-1, 1));
                    $return[3]=array_sum(array_slice($this->freqValid, $medianN));
                }
                break;
            default:
                echo 'no valid type, not implemented yet';
                break;

        }
        return $return;
    }
    /*
     * compute variance, $type=sort of variance populatie/sample
    */
    protected function computeVariance ($type=NULL)
    {
        $sum=0;
        foreach ($this->freqValid as $key=>$value) {
            $sum+=($value*(pow(($key-$this->mean), 2)));
        }
        if ($type='sample') {
            return $sum/($this->totalValid - 1);
        } else {
            return $sum/$this->totalValid;

        }
    }

    protected function computeSkewness($type=Null)
    {
        //sample skew
        $sum=0;
        foreach ($this->freqValid as $key=>$value) {
            $sum+=$value*pow(($key-$this->mean), 3);
        }
        $skew=($sum/$this->totalValid)/(pow($this->standardDeviation, 3));
        //population correction
        if ($type<>'sample') {
            $correction = (sqrt($this->totalValid*($this->totalValid-1)))/($this->totalValid-2);
            $skew=$correction*$skew;
        }
        return $skew;
    }

    protected function isEven ($x)
    {
        return is_int($x/2);
    }
}