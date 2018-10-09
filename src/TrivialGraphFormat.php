<?php

namespace Graphp\TrivialGraphFormat;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Exporter\ExporterInterface;

/**
 * Trivial Graph Format (TGF) is a really simple format for describing graphs.
 *
 * It consists of a list of vertices, a separator and a list of directed edges
 * as vertex pairs. Each entry can only have a single plain text label assigned
 * which does not support any structured information.
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Trivial_Graph_Format
 */
class TrivialGraphFormat implements ExporterInterface
{
    const EOL = PHP_EOL;

    /**
     * Exports the given graph instance.
     *
     * ```php
     * $graph = new Fhaculty\Graph\Graph();
     *
     * $a = $graph->createVertex('a');
     * $b = $graph->createVertex('b');
     * $a->createEdgeTo($b);
     *
     * $exporter = new Graphp\TrivialGraphFormat\TrivialGraphFormat();
     * $data = $exporter->getOutput($graph);
     *
     * file_put_contents('example.tgf', $data);
     * echo $data;
     * ```
     *
     * The TGF output will look something like this:
     *
     * ```
     * 1 a
     * 2 b
     * #
     * 1 2
     * ```
     *
     * This method only supports exporting the basic graph structure, with all
     * vertices and directed and undirected edges.
     *
     * Note that TGF does not support the notion of directed and undirected
     * edges. As such, this method will print two edges in opposite directions
     * for any undirected edges.
     *
     * Note that TGF does not support the notion of structured attributes. As
     * such, this method will print the mathematical attributes as vertex and
     * edge labels as appropriate.
     *
     * @param Graph $graph
     * @return string
     */
    public function getOutput(Graph $graph)
    {
        $output = '';

        // build an array to map vertex IDs (which may contain complex strings) to temporary numeric IDs for output
        $tid = 1;
        $tids = array();

        foreach ($graph->getVertices()->getMap() as $vid => $vertex) {
            $output .= $tid . ' ' . $this->getVertexLabel($vertex) . self::EOL;
            $tids[$vid] = $tid++;
        }

        // end of vertex list, start of edge list
        $output .= '#' . self::EOL;

        foreach ($graph->getEdges() as $edge) {
            /* @var $edge Edge */
            $ids = $edge->getVertices()->getIds();
            $a = $tids[$ids[0]];
            $b = $tids[$ids[1]];

            $label = $this->getEdgeLabel($edge);
            if ($label !== '') {
                $label = ' ' . $label;
            }

            $output .= $a . ' ' . $b . $label . self::EOL;

            // this is not a directed edge => also add back-edge with same label
            if (!($edge instanceof Directed)) {
                $output .= $b . ' ' . $a . $label . self::EOL;
            }
        }
        return $output;
    }

    /**
     * get label for given $vertex
     *
     * @param Vertex $vertex
     * @return string
     */
    protected function getVertexLabel(Vertex $vertex)
    {
        // label defaults to the vertex ID
        $label = $vertex->getId();

        // add balance to label if set
        $balance = $vertex->getBalance();
        if($balance !== NULL){
            if($balance > 0){
                $balance = '+' . $balance;
            }
            $label.= ' (' . $balance . ')';
        }

        // add group to label if set
        // TODO: what does 'if set' mean? groups should not be shown when vertex never had any group assigned (but it defaults to 0)
//         $group = $vertex->getGroup();
//         if ($group !== 0) {
//             $label .= ' [' . $group .']';
//         }

        return $label;
    }

    /**
     * get label for given $edge
     *
     * @param Edge $edge
     * @return string label (may be empty if there's nothing to be described)
     */
    protected function getEdgeLabel(Edge $edge)
    {
        $label = '';

        $flow = $edge->getFlow();
        $capacity = $edge->getCapacity();
        // flow is set
        if ($flow !== NULL) {
            // NULL capacity = infinite capacity
            $label = $flow . '/' . ($capacity === NULL ? '∞' : $capacity);
        // capacity set, but not flow (assume zero flow)
        } elseif ($capacity !== NULL) {
            $label = '0/' . $capacity;
        }

        $weight = $edge->getWeight();
        // weight is set
        if ($weight !== NULL) {
            if ($label === '') {
                $label = $weight;
            } else {
                $label .= '/' . $weight;
            }
        }
        return $label;
    }
}
