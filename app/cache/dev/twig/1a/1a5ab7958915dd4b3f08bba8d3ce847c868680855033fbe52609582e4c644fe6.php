<?php

/* TwigBundle:Exception:exception_full.html.twig */
class __TwigTemplate_ac577ee8ea3daf9edb64bbae735efd5cb8bc9900c24c395034f3d4e8b5acfdbb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("TwigBundle::layout.html.twig", "TwigBundle:Exception:exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "TwigBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_b3e4e3ea00e2bb54637a27afb4c6e10f0e540db2d73df6793018fbb9344bab4d = $this->env->getExtension("native_profiler");
        $__internal_b3e4e3ea00e2bb54637a27afb4c6e10f0e540db2d73df6793018fbb9344bab4d->enter($__internal_b3e4e3ea00e2bb54637a27afb4c6e10f0e540db2d73df6793018fbb9344bab4d_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "TwigBundle:Exception:exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_b3e4e3ea00e2bb54637a27afb4c6e10f0e540db2d73df6793018fbb9344bab4d->leave($__internal_b3e4e3ea00e2bb54637a27afb4c6e10f0e540db2d73df6793018fbb9344bab4d_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_1897b5b4fba76b5acd1e2e9f86f8c7f104de8ee8cb2b87056c057140d0bfa7fc = $this->env->getExtension("native_profiler");
        $__internal_1897b5b4fba76b5acd1e2e9f86f8c7f104de8ee8cb2b87056c057140d0bfa7fc->enter($__internal_1897b5b4fba76b5acd1e2e9f86f8c7f104de8ee8cb2b87056c057140d0bfa7fc_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('request')->generateAbsoluteUrl($this->env->getExtension('asset')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_1897b5b4fba76b5acd1e2e9f86f8c7f104de8ee8cb2b87056c057140d0bfa7fc->leave($__internal_1897b5b4fba76b5acd1e2e9f86f8c7f104de8ee8cb2b87056c057140d0bfa7fc_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_b2c1361df947b49a69a6c31df22a680e3f18252934ef6cc4cff6c9bc48643264 = $this->env->getExtension("native_profiler");
        $__internal_b2c1361df947b49a69a6c31df22a680e3f18252934ef6cc4cff6c9bc48643264->enter($__internal_b2c1361df947b49a69a6c31df22a680e3f18252934ef6cc4cff6c9bc48643264_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_b2c1361df947b49a69a6c31df22a680e3f18252934ef6cc4cff6c9bc48643264->leave($__internal_b2c1361df947b49a69a6c31df22a680e3f18252934ef6cc4cff6c9bc48643264_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_65971a3d1dae6c0c0079dd88cae3240204a5f455ece74e61fa2eff5f2e95615f = $this->env->getExtension("native_profiler");
        $__internal_65971a3d1dae6c0c0079dd88cae3240204a5f455ece74e61fa2eff5f2e95615f->enter($__internal_65971a3d1dae6c0c0079dd88cae3240204a5f455ece74e61fa2eff5f2e95615f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("TwigBundle:Exception:exception.html.twig", "TwigBundle:Exception:exception_full.html.twig", 12)->display($context);
        
        $__internal_65971a3d1dae6c0c0079dd88cae3240204a5f455ece74e61fa2eff5f2e95615f->leave($__internal_65971a3d1dae6c0c0079dd88cae3240204a5f455ece74e61fa2eff5f2e95615f_prof);

    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }
}
/* {% extends 'TwigBundle::layout.html.twig' %}*/
/* */
/* {% block head %}*/
/*     <link href="{{ absolute_url(asset('bundles/framework/css/exception.css')) }}" rel="stylesheet" type="text/css" media="all" />*/
/* {% endblock %}*/
/* */
/* {% block title %}*/
/*     {{ exception.message }} ({{ status_code }} {{ status_text }})*/
/* {% endblock %}*/
/* */
/* {% block body %}*/
/*     {% include 'TwigBundle:Exception:exception.html.twig' %}*/
/* {% endblock %}*/
/* */
