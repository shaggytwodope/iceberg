{% extends "base.tpl" %}

{% block title %}{{ title }}{% endblock %}

{% block meta %} {{ parent() }}
			<meta name="author" content="{{ title }}">{% endblock %}

{% block page %}
			<article>
				<h1>{{ title }}</h1>
				<em>by {{ author }} on {{ date|date("Y/m/d", false) }}</em>
				{{ content }}
			</article>
{% endblock %}
