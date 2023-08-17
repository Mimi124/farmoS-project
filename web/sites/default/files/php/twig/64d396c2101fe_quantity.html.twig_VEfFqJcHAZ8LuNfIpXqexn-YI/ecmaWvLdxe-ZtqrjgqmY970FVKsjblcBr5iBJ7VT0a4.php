<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* profiles/farm/modules/core/quantity/templates/quantity.html.twig */
class __TwigTemplate_e90bac1609655d096ad60c7f805cdaef extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 19
        echo "
";
        // line 21
        $context["custom_fields"] = $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter($this->sandbox->ensureToStringAllowed(($context["content"] ?? null), 21, $this->source), "label", "measure", "value", "units");
        // line 22
        $context["prefix_fields"] = twig_array_filter($this->env, $this->sandbox->ensureToStringAllowed(($context["custom_fields"] ?? null), 22, $this->source), function ($__field__) use ($context, $macros) { $context["field"] = $__field__; return ((($__internal_compile_0 = ($context["field"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["#weight"] ?? null) : null) && ((($__internal_compile_1 = ($context["field"] ?? null)) && is_array($__internal_compile_1) || $__internal_compile_1 instanceof ArrayAccess ? ($__internal_compile_1["#weight"] ?? null) : null) < 0)); });
        // line 23
        $context["suffix_fields"] = twig_array_filter($this->env, $this->sandbox->ensureToStringAllowed(($context["custom_fields"] ?? null), 23, $this->source), function ($__field__) use ($context, $macros) { $context["field"] = $__field__; return ( !(($__internal_compile_2 = ($context["field"] ?? null)) && is_array($__internal_compile_2) || $__internal_compile_2 instanceof ArrayAccess ? ($__internal_compile_2["#weight"] ?? null) : null) || ((($__internal_compile_3 = ($context["field"] ?? null)) && is_array($__internal_compile_3) || $__internal_compile_3 instanceof ArrayAccess ? ($__internal_compile_3["#weight"] ?? null) : null) >= 0)); });
        // line 24
        echo "
<div";
        // line 25
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => "quantity"], "method", false, false, true, 25), 25, $this->source), "html", null, true);
        echo ">
  ";
        // line 26
        if (($context["content"] ?? null)) {
            // line 27
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["prefix_fields"] ?? null), 27, $this->source), "html", null, true);
            echo "
    ";
            // line 28
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "label", [], "any", false, false, true, 28)) {
                echo "<strong>";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "label", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
                echo "</strong>";
            }
            // line 29
            echo "    ";
            if (twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "measure", [], "any", false, false, true, 29)) {
                echo "(";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "measure", [], "any", false, false, true, 29), 29, $this->source), "html", null, true);
                echo ")";
            }
            // line 30
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "value", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
            echo " ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "units", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
            echo "
    ";
            // line 31
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["suffix_fields"] ?? null), 31, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 33
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "profiles/farm/modules/core/quantity/templates/quantity.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 33,  82 => 31,  75 => 30,  68 => 29,  62 => 28,  57 => 27,  55 => 26,  51 => 25,  48 => 24,  46 => 23,  44 => 22,  42 => 21,  39 => 19,);
    }

    public function getSourceContext()
    {
        return new Source("", "profiles/farm/modules/core/quantity/templates/quantity.html.twig", "/home/mims/project/web/profiles/farm/modules/core/quantity/templates/quantity.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 21, "if" => 26);
        static $filters = array("without" => 21, "filter" => 22, "escape" => 25);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['without', 'filter', 'escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
