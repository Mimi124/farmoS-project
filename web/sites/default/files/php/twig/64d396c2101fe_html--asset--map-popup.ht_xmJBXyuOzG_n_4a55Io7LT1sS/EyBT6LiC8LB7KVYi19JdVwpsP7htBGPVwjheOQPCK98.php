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

/* profiles/farm/modules/core/ui/theme/templates/html--asset--map-popup.html.twig */
class __TwigTemplate_22869c3fbd5a94225b59cde7bfd29b34 extends \Twig\Template
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
        // line 15
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("farm_ui_theme/asset_map_popup"), "html", null, true);
        echo "
";
        // line 17
        $context["body_classes"] = [0 => ((        // line 18
($context["logged_in"] ?? null)) ? ("user-logged-in") : ("")), 1 => (( !        // line 19
($context["root_path"] ?? null)) ? ("path-frontpage") : (("path-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["root_path"] ?? null), 19, $this->source))))), 2 => ((        // line 20
($context["node_type"] ?? null)) ? (("page-node-type-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["node_type"] ?? null), 20, $this->source)))) : ("")), 3 => ((        // line 21
($context["db_offline"] ?? null)) ? ("db-offline") : (""))];
        // line 24
        echo "<!DOCTYPE html>
<html";
        // line 25
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["html_attributes"] ?? null), 25, $this->source), "html", null, true);
        echo ">
  <head>
    <head-placeholder token=\"";
        // line 27
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["placeholder_token"] ?? null), 27, $this->source), "html", null, true);
        echo "\">
    <title>";
        // line 28
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->safeJoin($this->env, $this->sandbox->ensureToStringAllowed(($context["head_title"] ?? null), 28, $this->source), " | "));
        echo "</title>
    <css-placeholder token=\"";
        // line 29
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["placeholder_token"] ?? null), 29, $this->source), "html", null, true);
        echo "\">
    <js-placeholder token=\"";
        // line 30
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["placeholder_token"] ?? null), 30, $this->source), "html", null, true);
        echo "\">

    ";
        // line 33
        echo "    <base target=\"_parent\">
  </head>
  <body";
        // line 35
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["body_classes"] ?? null)], "method", false, false, true, 35), 35, $this->source), "html", null, true);
        echo ">
    ";
        // line 36
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["page"] ?? null), 36, $this->source), "html", null, true);
        echo "
    <js-bottom-placeholder token=\"";
        // line 37
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["placeholder_token"] ?? null), 37, $this->source), "html", null, true);
        echo "\">
  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "profiles/farm/modules/core/ui/theme/templates/html--asset--map-popup.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 37,  82 => 36,  78 => 35,  74 => 33,  69 => 30,  65 => 29,  61 => 28,  57 => 27,  52 => 25,  49 => 24,  47 => 21,  46 => 20,  45 => 19,  44 => 18,  43 => 17,  39 => 15,);
    }

    public function getSourceContext()
    {
        return new Source("", "profiles/farm/modules/core/ui/theme/templates/html--asset--map-popup.html.twig", "/home/mims/project/web/profiles/farm/modules/core/ui/theme/templates/html--asset--map-popup.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 17);
        static $filters = array("escape" => 15, "clean_class" => 19, "safe_join" => 28);
        static $functions = array("attach_library" => 15);

        try {
            $this->sandbox->checkSecurity(
                ['set'],
                ['escape', 'clean_class', 'safe_join'],
                ['attach_library']
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
