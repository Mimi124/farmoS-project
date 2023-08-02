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

/* profiles/farm/modules/core/ui/theme/templates/menu-local-task--secondary.html.twig */
class __TwigTemplate_81f3c8df3d0e09840a05bd3a9b623090 extends \Twig\Template
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
        // line 21
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("farm_ui_theme/menu_local_task"), "html", null, true);
        echo "
";
        // line 23
        $context["classes"] = [0 => "tabs__tab", 1 => "js-tab", 2 => ((        // line 26
($context["is_active"] ?? null)) ? ("is-active") : ("")), 3 => ((        // line 27
($context["is_active"] ?? null)) ? ("js-active-tab") : (""))];
        // line 30
        echo "<li";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 30), 30, $this->source), "html", null, true);
        echo ">
  ";
        // line 31
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["link"] ?? null), 31, $this->source), "html", null, true);
        echo "
  ";
        // line 32
        if (($context["is_active"] ?? null)) {
            // line 33
            echo "    <button class=\"reset-appearance tabs__trigger\" aria-label=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Tabs display toggle"));
            echo "\" data-drupal-nav-tabs-trigger>
      ";
            // line 34
            $this->loadTemplate("@claro/../images/src/hamburger-menu.svg", "profiles/farm/modules/core/ui/theme/templates/menu-local-task--secondary.html.twig", 34)->display($context);
            // line 35
            echo "    </button>
  ";
        }
        // line 37
        echo "</li>
";
    }

    public function getTemplateName()
    {
        return "profiles/farm/modules/core/ui/theme/templates/menu-local-task--secondary.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 37,  65 => 35,  63 => 34,  58 => 33,  56 => 32,  52 => 31,  47 => 30,  45 => 27,  44 => 26,  43 => 23,  39 => 21,);
    }

    public function getSourceContext()
    {
        return new Source("", "profiles/farm/modules/core/ui/theme/templates/menu-local-task--secondary.html.twig", "/home/mims/project/web/profiles/farm/modules/core/ui/theme/templates/menu-local-task--secondary.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 23, "if" => 32, "include" => 34);
        static $filters = array("escape" => 21, "t" => 33);
        static $functions = array("attach_library" => 21);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'include'],
                ['escape', 't'],
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
