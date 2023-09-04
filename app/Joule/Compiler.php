<?php

namespace App\Joule;

use Illuminate\Support\Str;
use Stillat\BladeParser\Document\Document;
use Stillat\BladeParser\Nodes\AbstractNode;
use Stillat\BladeParser\Nodes\Components\ComponentNode;

class Compiler
{
    protected ComponentCompiler $componentCompiler;

    /**
     * @var CompiledComponent[]
     */
    protected array $components = [];

    public function __construct()
    {
        $this->componentCompiler = new ComponentCompiler($this);
    }

    protected function isVoltComponent(mixed $node): bool
    {
        return $node instanceof ComponentNode &&
               $node->componentPrefix == 'v' &&
               $node->name == 'volt';
    }

    /**
     * @param  AbstractNode[]  $nodes
     */
    public function compileVolt(array $nodes): string
    {
        $compiled = '';

        $skipTo = null;

        foreach ($nodes as $node) {
            if ($skipTo) {
                // Reset the skipTo node if we've reached it.
                if ($node === $skipTo) {
                    $skipTo = null;
                }

                continue;
            }

            if ($this->isVoltComponent($node)) {
                /** @var ComponentNode $node */
                $component = $this->componentCompiler->compile($node);

                $compiled .= '@livewire(\''.$component->name.'\')';

                $this->components[] = $component;

                // We want to skip over all the component's children.
                $skipTo = $node->isClosedBy;
            } else {
                $compiled .= (string) $node;
            }
        }

        return $compiled;
    }

    public function compile(string $input): string
    {
        // Prevent compiling input that has already been compiled.
        if (Str::contains($input, ['__ENDBLOCK__ ', '{{-- __joule --}}'])) {
            return $input;
        }

        $nodes = Document::fromText($input, customComponentTags: ['v'])
            ->resolveStructures()
            ->getNodes()
            ->all();

        $result = $this->compileVolt($nodes);

        foreach ($this->components as $component) {
            $path = storage_path('framework/views/compiled_inline_livewire/').$component->name.'.blade.php';

            file_put_contents($path, $component->class);
        }

        return $result;
    }
}
