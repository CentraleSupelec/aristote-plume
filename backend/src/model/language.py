from enum import Enum
from pydantic import BaseModel


class Language(Enum):
    FRENCH = "fr"
    ENGLISH = "en"


class LanguageProperties(BaseModel):
    name: str
    write_prompt: str
    ask_prompt: str
    search_prompt: str
    plan_prompt: str
    respond_prompt: str


LANGUAGES = {
    "fr": LanguageProperties(
        name="French",
        write_prompt="Veuillez écrire en Français",
        ask_prompt="Veuillez poser des questions en Français",
        search_prompt="Veuillez rechercher en Français",
        plan_prompt="Générer un plan en Français",
        respond_prompt="Veuillez répondre en Français",
    ),
    "en": LanguageProperties(
        name="English",
        write_prompt="Please write in English",
        ask_prompt="Please ask questions in English",
        search_prompt="Please search in English",
        plan_prompt="Generate a plan in English",
        respond_prompt="Please respond in English",
    ),
}
