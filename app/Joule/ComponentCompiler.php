<?php

namespace App\Joule;

use Illuminate\Support\Str;
use Stillat\BladeParser\Nodes\Components\ComponentNode;
use Stillat\BladeParser\Nodes\Components\ParameterNode;
use Stillat\BladeParser\Nodes\Components\ParameterType;

class ComponentCompiler
{
    private static int $componentCount = 0;

    protected Compiler $voltCompiler;

    public function __construct(Compiler $voltCompiler)
    {
        $this->voltCompiler = $voltCompiler;
    }

    private function getComponentName(): string
    {
        return 'joule_'.self::$componentCount++;
    }

    protected function compilePublicMethod(ParameterNode $param): string
    {
        $methodName = Str::after($param->name, '@');
        $methodBody = trim($param->value);

        if (Str::startsWith($methodBody, 'fn(')) {
            $methodBody = trim(Str::after($methodBody, '=>'));
        }

        $methodBody = Str::finish($methodBody, ';');

        return strtr(<<<'METHOD'
    public function %name%()
    {
        %body%
    }

METHOD,
            [
                '%name%' => $methodName,
                '%body%' => $methodBody,
            ]
        );
    }

    public function compile(ComponentNode $componentNode): CompiledComponent
    {
        $name = $this->getComponentName();

        $innerContent = $this->voltCompiler->compileVolt($componentNode->childNodes);
        $publicMethods = [];
        $publicProperties = [];

        /** @var ParameterNode $param */
        foreach ($componentNode->getParameters() as $param) {
            if ($param->type == ParameterType::Parameter) {
                if (Str::startsWith($param->name, '@') || Str::startsWith(trim($param->value), 'fn(')) {
                    $publicMethods[] = $this->compilePublicMethod($param);
                } else {
                    // A parameter's materialized name will have any leading symbols removed.
                    $publicProperties[] = '    public $'.$param->materializedName.' = '.$param->value.';';
                }
            } elseif ($param->type == ParameterType::DynamicVariable) {
                $publicProperties[] = '    public $'.$param->materializedName.' = '.$param->value.';';
            }
        }

        $classStub = <<<'CLASS'
<?php

use Livewire\Volt\Component;

new class extends Component
{
%props%

%methods%
}
?>

<div>
{{-- __joule --}}
%template%
</div>
CLASS;

        $class = strtr($classStub, [
            '%props%' => implode("\n", $publicProperties),
            '%methods%' => implode("\n", $publicMethods),
            '%template%' => $innerContent,
        ]);

        return new CompiledComponent($name, $class);
    }
}
