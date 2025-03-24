# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.2] - 2025-06-03

### Added

- ✨ (webapp) Application and admin areas
- ✨ (webapp) OIDC authentication (as administrator and regular user)
- ✨ (webapp) Articles list view
- ✨ (webapp) Article creation page, and article loading page
- ✨ (python worker) Article generation in english / french based on Arxiv data (using `knowledge_storm` module,
with selection of one model for all tasks)
- ✨ (python api) Triggering and monitoring of worker's `celery` tasks (article generation tasks)
